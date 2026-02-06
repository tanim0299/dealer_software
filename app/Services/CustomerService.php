<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\DriverArea;

class CustomerService
{
    public function CustomerList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Customer())->CustomerList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeCustomer($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::customerRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Customer())->createCustomer($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Customer created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getCustomerById($id)
    {
        $status_code = $status_message = $customer = '';
        try {
            $customer = (new Customer())->getCustomerById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $customer];
    }

    public function updateCustomer($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::customerRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Customer())->updateCustomer($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Customer updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteCustomer($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getCustomerById($id);
            Customer::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Customer deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateCustomerStatus($id)
    {
        $status_code = $status_message = null;
        try {
            (new Customer())->updateStatus($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Customer status updated successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function getrDriverCustomer($driver_id)
    {
        $status_code = $status_message = $response = '';
        try {
            $driverAreas = DriverArea::where('driver_id', $driver_id)
                ->pluck('area_id')
                ->toArray();

            $response = Customer::whereIn('area_id', $driverAreas)->get();
            $status_code = ApiService::API_SUCCESS;
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }
}
