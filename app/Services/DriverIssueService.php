<?php

namespace App\Services;

use App\Models\DriverIssueItem;
use App\Models\DriverIssues;
use App\Models\WareHouseStocks;
use Exception;
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
        $status_code = $status_message = $response =  '';
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
                $issue = DriverIssues::create([
                    'driver_id' => $request->driver_id,
                    'issue_date' => now(),
                    'status' => 'open',
                ]);

                foreach ($request->items as $item) {

                    $requiredQty = $item['issue_qty'];
                    $totalCost = 0;
                    $totalIssued = 0;

                    $stocks = WareHouseStocks::where('product_id', $item['product_id'])
                        ->lockForUpdate()
                        ->orderBy('created_at', 'asc') // FIFO
                        ->get();

                    // Calculate total available stock first
                    $totalAvailable = $stocks->sum(function ($stock) {
                        return $stock->purchase_qty
                            + $stock->sales_return_qty
                            - $stock->sales_qty
                            + $stock->return_qty
                            - $stock->sr_issue_qty;
                    });

                    if ($totalAvailable < $requiredQty) {
                        throw new Exception('Stock not sufficient');
                    }

                    foreach ($stocks as $stock) {

                        $availableQty = $stock->purchase_qty
                            + $stock->sales_return_qty
                            - $stock->sales_qty
                            + $stock->return_qty
                            - $stock->sr_issue_qty;

                        if ($availableQty <= 0) {
                            continue;
                        }

                        $issueFromThisStock = min($availableQty, $requiredQty);

                        // Accumulate cost
                        $totalCost += $issueFromThisStock * $stock->purchase_price;
                        $totalIssued += $issueFromThisStock;

                        // Increment sr_issue_qty FIFO-wise
                        $stock->increment('sr_issue_qty', $issueFromThisStock);

                        $requiredQty -= $issueFromThisStock;

                        if ($requiredQty <= 0) {
                            break;
                        }
                    }

                    // Weighted average purchase price
                    $averagePrice = $totalCost / $totalIssued;

                    DriverIssueItem::create([
                        'driver_issue_id' => $issue->id,
                        'product_id'      => $item['product_id'],
                        'issue_qty'       => $totalIssued,
                        'purchase_price'       => $averagePrice,
                    ]);
                }

                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Driver Issue Created';
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

                // Old items indexed by product_id
                $oldItems = $issue->items->keyBy('product_id');

                foreach ($request->items as $item) {

                    $productId = $item['product_id'];
                    $newQty    = (float) $item['issue_qty'];

                    $oldQty = $oldItems[$productId]->issue_qty ?? 0;

                    $difference = $newQty - $oldQty;

                    if ($difference == 0) {
                        continue; // no change
                    }

                    $stock = WareHouseStocks::where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();

                    if (!$stock) {
                        throw new \Exception('Stock not found');
                    }

                    // AVAILABLE STOCK (exclude current issue qty)
                    $availableStock =
                        ($stock->purchase_qty + $stock->sales_return_qty)
                        - (
                            $stock->sales_qty
                            + $stock->return_qty
                            + $stock->sr_issue_qty
                            - $oldQty // VERY IMPORTANT
                        );

                    // If increasing qty
                    if ($difference > 0) {

                        if ($availableStock < $difference) {
                            throw new \Exception('Insufficient stock for product');
                        }

                        $stock->increment('sr_issue_qty', $difference);
                    }

                    // If decreasing qty
                    if ($difference < 0) {
                        $stock->decrement('sr_issue_qty', abs($difference));
                    }

                    // Update or create issue item
                    DriverIssueItem::updateOrCreate(
                        [
                            'driver_issue_id' => $issue->id,
                            'product_id'      => $productId,
                        ],
                        [
                            'issue_qty' => $newQty
                        ]
                    );
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
             * 🔄 Rollback issued qty to warehouse
             */
            foreach ($issue->items as $item) {

                $stock = WareHouseStocks::where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    // subtract issued qty
                    $stock->decrement('sr_issue_qty', $item->issue_qty);
                }
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
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = 'Driver Issue Removed';
        } catch (\Throwable $th) {
            DB::rollBack();
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}