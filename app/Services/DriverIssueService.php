<?php

namespace App\Services;

use App\Models\DriverIssueItem;
use App\Models\DriverIssues;
use App\Models\WareHouseStocks;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class DriverIssueService {

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
                $ft = '%' . $search['free_text'] . '%';
                $query->where(function ($q) use ($ft) {
                    $q->whereHas('driver', function ($driverQuery) use ($ft) {
                        $driverQuery->where('name', 'like', $ft)
                            ->orWhere('phone', 'like', $ft)
                            ->orWhere('vehicle_no', 'like', $ft);
                    });
                });
            }

            if (!empty($search['status'])) {
                $query->where('status', $search['status']);
            }

            if (!empty($search['from_date'])) {
                $query->whereDate('issue_date', '>=', $search['from_date']);
            }

            if (!empty($search['to_date'])) {
                $query->whereDate('issue_date', '<=', $search['to_date']);
            }

            if (!empty($search['driver_id'])) {
                $query->where('driver_id', $search['driver_id']);
            }

            if ($is_paginate) {
                $issues = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
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
                        'cash_from_manager' => 0,
                        'status' => 'open',
                    ]);
                }

                if ($issue->status === 'rejected') {
                    DriverIssueItem::where('driver_issue_id', $issue->id)->delete();
                    $issue->update([
                        'status' => 'open',
                        'cash_from_manager' => 0,
                    ]);
                    $issue->refresh();
                }

                // One warehouse submission per DSR per calendar day: first POST creates lines;
                // further changes must use Edit (update), not a second Create POST — unless issue is already accepted (append below).
                if ($issue->status === 'open' && $issue->items()->count() > 0) {
                    throw new \Exception(
                        'Stock has already been issued to this DSR for this date. Use Edit on the issue list to change lines before the DSR accepts, or delete the open issue first.'
                    );
                }

                $appendToAccepted = $issue->status === 'accepted';

                $groupedItems = collect($request->items)
                    ->groupBy('product_id')
                    ->map(function ($rows) {
                        return [
                            'product_id' => $rows->first()['product_id'],
                            'issue_qty' => $rows->sum('issue_qty'),
                        ];
                    })->values();

                foreach ($groupedItems as $item) {
                    $productId = (int) $item['product_id'];
                    $addQty = (float) $item['issue_qty'];

                    if ($addQty <= 0) {
                        continue;
                    }

                    $slices = WareHouseStocks::planFifoSlicesForProduct($productId, $addQty);

                    foreach ($slices as $slice) {
                        $sliceQty = (float) $slice['qty'];
                        $sliceWhId = (int) $slice['warehouse_stock_id'];
                        $normPrice = WareHouseStocks::normalizePurchasePrice($slice['purchase_price']);

                        if ($appendToAccepted) {
                            $existing = DriverIssueItem::query()
                                ->where('driver_issue_id', $issue->id)
                                ->where('product_id', $productId)
                                ->whereRaw('ROUND(purchase_price, 4) = ?', [$normPrice])
                                ->lockForUpdate()
                                ->first();

                            if ($existing) {
                                $existing->increment('issue_qty', $sliceQty);
                                WareHouseStocks::applyDriverIssueDeductionOnWarehouseRow(
                                    $sliceWhId,
                                    $productId,
                                    $sliceQty
                                );

                                continue;
                            }
                        }

                        DriverIssueItem::create([
                            'driver_issue_id' => $issue->id,
                            'product_id' => $productId,
                            'warehouse_stock_id' => $sliceWhId,
                            'issue_qty' => $sliceQty,
                            'sold_qty' => 0,
                            'return_qty' => 0,
                            'purchase_price' => $slice['purchase_price'],
                            'sale_price' => $slice['sale_price'],
                        ]);

                        if ($appendToAccepted) {
                            WareHouseStocks::applyDriverIssueDeductionOnWarehouseRow(
                                $sliceWhId,
                                $productId,
                                $sliceQty
                            );
                        }
                    }
                }

                $status_code = ApiService::API_SUCCESS;
                $status_message = $appendToAccepted
                    ? 'Additional stock was added under the same accepted issue for this date. Warehouse stock was updated automatically; no DSR accept is required.'
                    : 'DSR stock issue sent. The DSR will see it in their app for acceptance.';

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
                    'cash_from_manager' => 0,
                ]);

                DriverIssueItem::where('driver_issue_id', $issue->id)->delete();

                foreach ($incomingItems as $productId => $item) {
                    $newQty = (float) ($item['issue_qty'] ?? 0);
                    if ($newQty <= 0) {
                        throw new \Exception('Issue quantity must be greater than zero.');
                    }

                    $slices = WareHouseStocks::planFifoSlicesForProduct((int) $productId, $newQty);

                    foreach ($slices as $slice) {
                        DriverIssueItem::create([
                            'driver_issue_id' => $issue->id,
                            'product_id' => (int) $productId,
                            'warehouse_stock_id' => $slice['warehouse_stock_id'],
                            'issue_qty' => $slice['qty'],
                            'sold_qty' => 0,
                            'return_qty' => 0,
                            'purchase_price' => $slice['purchase_price'],
                            'sale_price' => $slice['sale_price'],
                        ]);
                    }
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