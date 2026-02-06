<?php
namespace App\Services;

use App\Models\DriverIssueItem;
use App\Models\WareHouseStocks;
use Illuminate\Support\Facades\DB;

class StockService {
    public function getWarehouseStocks($search = [], $is_paginate = true, $is_relation = false)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new WareHouseStocks())->getStockList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Stock Data Found';
        } catch (\Throwable $th) {
            $status_code =  ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code , $status_message, $response];
    }

    public function getDriverStock($driver_id, $date)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = DriverIssueItem::query()
                ->select(
                    'product_id',
                    DB::raw('SUM(issue_qty) as issue_qty'),
                    DB::raw('SUM(sold_qty) as sold_qty'),
                    DB::raw('SUM(return_qty) as return_qty'),
                    DB::raw('
                        SUM(issue_qty)
                        - SUM(sold_qty)
                        - SUM(return_qty)
                        AS available_qty
                    ')
                )
                ->whereHas('driverIssue', function ($q) use ($driver_id, $date) {
                    $q->where('driver_id', $driver_id)
                    ->where('issue_date', $date)
                    ->where('status', 'open');
                })
                ->groupBy('product_id')
                ->havingRaw('available_qty > 0')
                ->with([
                    'product:id,name,sale_price,unit_id',   // 🔥 sale_price from products
                    'product.unit.sub_unit'                 // 🔥 sub units
                ])
                ->get();




            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Stock Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code, $status_message, $response];
    }
}