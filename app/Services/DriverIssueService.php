<?php

namespace App\Services;

use App\Models\DriverIssueItem;
use App\Models\DriverIssues;
use App\Models\WareHouseStocks;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class DriverIssueService {

    private function allocateAcceptedStock($productId, $requiredQty)
    {
        $stocks = WareHouseStocks::where('product_id', $productId)
            ->lockForUpdate()
            ->orderBy('created_at', 'asc')
            ->get();

        $totalAvailable = $stocks->sum(function ($stock) {
            return $stock->purchase_qty
                + $stock->sales_return_qty
                - $stock->sales_qty
                - $stock->return_qty
                - $stock->sr_issue_qty;
        });

        if ($totalAvailable < $requiredQty) {
            throw new \Exception('Stock not sufficient');
        }

        $remainingQty = $requiredQty;
        $totalCost = 0;
        $totalSale = 0;
        $totalIssued = 0;

        foreach ($stocks as $stock) {
            if ($remainingQty <= 0) {
                break;
            }

            $availableQty = $stock->purchase_qty
                + $stock->sales_return_qty
                - $stock->sales_qty
                - $stock->return_qty
                - $stock->sr_issue_qty;

            if ($availableQty <= 0) {
                continue;
            }

            $issueFromThisStock = min($availableQty, $remainingQty);

            $stock->increment('sr_issue_qty', $issueFromThisStock);

            $totalCost += $issueFromThisStock * $stock->purchase_price;
            $totalSale += $issueFromThisStock * ($stock->sale_price ?? 0);
            $totalIssued += $issueFromThisStock;

            $remainingQty -= $issueFromThisStock;
        }

        if ($totalIssued <= 0) {
            throw new \Exception('Stock not sufficient');
        }

        return [
            'purchase_price' => $totalCost / $totalIssued,
            'sale_price' => $totalSale / $totalIssued,
        ];
    }

    public function getIssueDataById($id)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = DriverIssues::with([
                        'driver',
                        'items.product'
                    ])->findOrFail($id);

            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Driver Issue Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code , $status_message, $response];
    }

    public function getDriverIssueList($search = [], $is_paginate = false, $is_relation = false)
    {
        $status_code = $status_message = '';
        $response = $is_paginate ? null : collect();
        try {
            $query = DriverIssues::with('driver','items');
    
            if (!empty($search['free_text'])) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('driver', function ($driverQuery) use ($search) {
                        $driverQuery->where('name', 'like', '%' . $search['free_text'] . '%');
                    });
                });
            }

            if(!empty($search['driver_id']))
            {
                $query->where('driver_id',$search['driver_id']);   
            }
    
            if ($is_paginate) {
                $issues = $query->orderBy('id', 'desc')->paginate(10);
            } else {
                $issues = $query->orderBy('id', 'desc')->get();
            }
    
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Driver Issue List Retrieved';
            $response = $issues;
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
            $response = $is_paginate ? null : collect();
        } finally {
            return [$status_code, $status_message, $response];
        }
    }

    public function storeDriverIssue($request)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::driverIssueStoreRules();
        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if($validation[0] == ApiService::Api_VALIDATION_ERROR)
        {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
            return [$status_code, $status_message, $error_message];
        }
        else
        {
            try {
                DB::beginTransaction();
                $issueDate = !empty($request->issue_date) ? date('Y-m-d', strtotime($request->issue_date)) : now()->toDateString();

                $closedLedgerExists = DriverIssues::where('driver_id', $request->driver_id)
                    ->whereDate('issue_date', $issueDate)
                    ->where('status', 'closed')
                    ->exists();

                if ($closedLedgerExists) {
                    throw new \Exception('This driver issue ledger is closed for this date. You can not issue more stock.');
                }

                $issue = DriverIssues::where('driver_id', $request->driver_id)
                    ->whereDate('issue_date', $issueDate)
                    ->where('status', '!=', 'closed')
                    ->lockForUpdate()
                    ->first();

                if (!$issue) {
                    $issue = DriverIssues::create([
                        'driver_id' => $request->driver_id,
                        'issue_date' => $issueDate,
                        'cash_from_manager' => (float) ($request->cash_from_manager ?? 0),
                        'status' => 'open',
                    ]);
                } else {
                    $extraCash = (float) ($request->cash_from_manager ?? 0);
                    if ($extraCash > 0) {
                        $issue->increment('cash_from_manager', $extraCash);
                    }
                }

                if ($issue->status == 'rejected') {
                    $issue->update(['status' => 'open']);
                }

                $groupedItems = collect($request->items)
                    ->groupBy('product_id')
                    ->map(function ($rows) {
                        return [
                            'product_id' => $rows->first()['product_id'],
                            'issue_qty' => $rows->sum('issue_qty'),
                        ];
                    })->values();

                foreach ($groupedItems as $item) {
                    $productId = $item['product_id'];
                    $addQty = (float) $item['issue_qty'];

                    if ($addQty <= 0) {
                        continue;
                    }

                    $issueItem = DriverIssueItem::where('driver_issue_id', $issue->id)
                        ->where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();

                    if ($issue->status == 'accepted') {
                        $allocated = $this->allocateAcceptedStock($productId, $addQty);

                        if ($issueItem) {
                            $oldQty = (float) $issueItem->issue_qty;
                            $newQty = $oldQty + $addQty;

                            $newPurchasePrice = $newQty > 0
                                ? (($oldQty * (float) $issueItem->purchase_price) + ($addQty * (float) $allocated['purchase_price'])) / $newQty
                                : 0;

                            $newSalePrice = $newQty > 0
                                ? (($oldQty * (float) $issueItem->sale_price) + ($addQty * (float) $allocated['sale_price'])) / $newQty
                                : 0;

                            $issueItem->update([
                                'issue_qty' => $newQty,
                                'purchase_price' => $newPurchasePrice,
                                'sale_price' => $newSalePrice,
                            ]);
                        } else {
                            DriverIssueItem::create([
                                'driver_issue_id' => $issue->id,
                                'product_id'      => $productId,
                                'issue_qty'       => $addQty,
                                'sold_qty'        => 0,
                                'return_qty'      => 0,
                                'purchase_price'  => $allocated['purchase_price'],
                                'sale_price'      => $allocated['sale_price'],
                            ]);
                        }
                    } else {
                        if ($issueItem) {
                            $issueItem->increment('issue_qty', $addQty);
                        } else {
                            DriverIssueItem::create([
                                'driver_issue_id' => $issue->id,
                                'product_id'      => $productId,
                                'issue_qty'       => $addQty,
                                'purchase_price'  => 0,
                                'sale_price'      => 0,
                            ]);
                        }
                    }
                }

                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Driver Issue Updated';
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            } finally {
                return [$status_code, $status_message, $error_message];
            }
        
        }
    }

    public function updateDriverIssueById($request,$id)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::driverIssueStoreRules();
        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if($validation[0] == ApiService::Api_VALIDATION_ERROR)
        {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
            return [$status_code, $status_message, $error_message];
        }
        else
        {
            DB::beginTransaction();

            try {
                $issue = DriverIssues::with('items')
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($issue->status !== 'open') {
                    throw new \Exception('Issue already closed');
                }

                $incomingItems = collect($request->items)->keyBy('product_id');

                $issue->update([
                    'cash_from_manager' => (float) ($request->cash_from_manager ?? 0),
                ]);

                foreach ($incomingItems as $productId => $item) {
                    $newQty = (float) ($item['issue_qty'] ?? 0);
                    if ($newQty <= 0) {
                        throw new \Exception('Issue quantity must be greater than zero.');
                    }

                    $stock = WareHouseStocks::where('product_id', $productId)
                        ->lockForUpdate()
                        ->get();

                    $availableStock = $stock->sum(function ($row) {
                        return $row->purchase_qty + $row->sales_return_qty - $row->sales_qty - $row->return_qty - $row->sr_issue_qty;
                    });

                    if ($availableStock < $newQty) {
                        throw new \Exception('Insufficient stock for product.');
                    }

                    DriverIssueItem::updateOrCreate(
                        [
                            'driver_issue_id' => $issue->id,
                            'product_id'      => $productId,
                        ],
                        [
                            'issue_qty' => $newQty,
                            'sold_qty' => 0,
                            'return_qty' => 0,
                        ]
                    );
                }

                $existingProductIds = $issue->items->pluck('product_id');
                $incomingProductIds = $incomingItems->keys();
                $toDelete = $existingProductIds->diff($incomingProductIds);
                if ($toDelete->isNotEmpty()) {
                    DriverIssueItem::where('driver_issue_id', $issue->id)
                        ->whereIn('product_id', $toDelete->values())
                        ->delete();
                }


                DB::commit();

                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Driver Issue Updated';
                $error_message = '';

            } catch (\Exception $e) {
                DB::rollBack();

                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $e->getMessage();
                $error_message = '';
            }

            return [$status_code, $status_message, $error_message];
        }
    }

    public function deleteIssuebyId($id)
    {
        $status_code = $status_message = '';
        try {
            DB::beginTransaction();
            $issue = DriverIssues::with('items')
                ->lockForUpdate()
                ->findOrFail($id);

            // ❌ prevent delete if closed
            if ($issue->status !== 'open') {
                throw new \Exception('Only open issues can be deleted');
            }

            /**
             * 🧹 Delete issue items
             */
            DriverIssueItem::where('driver_issue_id', $issue->id)->delete();

            /**
             * 🗑 Delete main issue
             */
            $issue->delete();
            DB::commit();
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Driver Issue Removed';
        } catch (\Throwable $th) {
            DB::rollBack();
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}