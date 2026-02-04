<?php
namespace App\Services;

use App\Models\CustomerArea;
use Symfony\Component\HttpFoundation\Request;

class AreaService
{
    public function CustomerAreaList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new CustomerArea())->CustomerAreaList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeCustomerArea($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::CustomerAreaRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new CustomerArea())->createCustomerArea($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Customer Area created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getCustomerAreaById($id)
    {
        $status_code = $status_message = $CustomerArea = '';
        try {
            $CustomerArea = (new CustomerArea())->getCustomerAreaById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $CustomerArea];
    }

    public function updateCustomerArea($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::CustomerAreaRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new CustomerArea())->updateCustomerArea($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "CustomerArea updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteCustomerArea($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getCustomerAreaById($id);
            CustomerArea::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('CustomerArea deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateCustomerAreaStatus($request)
    {
        $status_code = $status_message = null;
        try {
            (new CustomerArea())->updateStatus($request);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "CustomerArea status changed successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
