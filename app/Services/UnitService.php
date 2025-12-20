<?php
namespace App\Services;

use App\Models\Unit;
use App\Services\ApiService;
use Illuminate\Http\Request;

class UnitService
{
    public function UnitList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Unit())->UnitList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeUnit($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::unitRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Unit())->createUnit($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Unit created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getUnitById($id)
    {
        $status_code = $status_message = $unit = '';
        try {
            $unit = (new Unit())->getUnitById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $unit];
    }

    public function updateUnit($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::unitRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Unit())->updateUnit($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Unit updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteUnit($id)
    {
        $status_code = $status_message = null;
        try {
            (new Unit())->deleteUnit($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Unit deleted successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateUnitStatus($id)
    {
        $status_code = $status_message = null;
        try {
            (new Unit())->updateStatus($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Unit status updated successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
