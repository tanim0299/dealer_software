<?php

namespace App\Services;

use App\Models\DriverIssueItem;
use App\Models\DriverIssues;
use App\Models\WareHouseStocks;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class DriverIssueService {

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

                    $stock = WareHouseStocks::where('product_id', $item['product_id'])->lockForUpdate()->first();

                    $currentStock = $stock->stock_qty;

                    if ($currentStock < $item['issue_qty']) {
                        throw new Exception('Stock not sufficient');
                    }

                    DriverIssueItem::create([
                        'driver_issue_id' => $issue->id,
                        'product_id'      => $item['product_id'],
                        'issue_qty'       => $item['issue_qty'],
                    ]);

                    $stock->increment('sr_issue_qty', $item['issue_qty']);
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
}