<?php
namespace App\Services;

use App\Models\WareHouseStocks;

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
}