<?php
namespace App\Services;

use App\Models\Product;

class ProductService {
    public function getProductList($search = [], $is_paginate = true, $is_relaiton = true)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Product())->getList($search, $is_paginate, $is_relaiton);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Product Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code, $status_message, $response];
    }
    public function storeProduct($request)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::productRules($request);
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
                (new Product())->createProduct($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Product Created';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            }
        }
        return [$status_code, $status_message, $error_message];
    }
    public function updateProductById($request, $id)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::productRules($request, $id);
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
                (new Product())->updateProduct($request,$id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Product Updated';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            }
        }
        return [$status_code, $status_message, $error_message];
    }

    public function getProductById($id)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Product())->find($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Product Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code,$status_message, $response];
    }

    public function deleteProductById($id)
    {
        $status_code = $status_message = '';
        try {
            (new Product())->deleteProductById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Product Removed';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code, $status_message];
    }
}