<?php
namespace App\Services;

use App\Models\Supplier;

class SupplierService {

    public function getSupplierList($search = [], $is_paginate = true, $is_relation = false)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Supplier())->getSupplierList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Suppliers Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }
    public function getSupplierById($id)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Supplier())->find($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Suppliers Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }
    public function deleteSupplierById($id)
    {
        $status_code = $status_message ='';
        try {
            (new Supplier())->where('id',$id)->delete();
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Suppliers Deleted';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function storeSupplier($request)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::supplierRules($request);
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
                (new Supplier())->storeSupplier($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Supplier Created';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            }
        }
        return [$status_code, $status_message, $error_message];
    }
    public function updateSupplierById($request,$id)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::supplierRules($request,$id);
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
                (new Supplier())->updateSupplierById($request,$id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Supplier Created';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            }
        }
        return [$status_code, $status_message, $error_message];
    }
}