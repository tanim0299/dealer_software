<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Drivers;

class DriverService {

    public function getDriverList($search = [], $is_paginate = true, $is_relation = false)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Drivers())->getrDriverList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Driver List Fetched';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        } finally {
            return [$status_code, $status_message, $response];
        }
    }

    public function storeDriver($request)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::driverStoreRules();
        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if($validation[0] == ApiService::Api_VALIDATION_ERROR)
        {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
           try {
            DB::beginTransaction();
            
            (new Drivers())->createDriver($request);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Driver Created';
            DB::commit();
           } catch (\Throwable $th) {
            DB::rollBack();
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
           } 
        }
        return [$status_code, $status_message, $error_message];
    }

}