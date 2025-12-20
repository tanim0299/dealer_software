<?php
namespace App\Services;

use App\Models\SubUnit;

class SubUnitService
{
    public function SubUnitList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new SubUnit())->SubUnitList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeSubUnit($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::subUnitRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new SubUnit())->createSubUnit($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Sub Unit created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getSubUnitById($id)
    {
        $status_code = $status_message = $subunit = '';
        try {
            $subunit = (new SubUnit())->getSubUnitById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $subunit];
    }

    public function updateSubUnit($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::subUnitRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new SubUnit())->updateSubUnit($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Sub Unit updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteSubUnit($id)
    {
        $status_code = $status_message = null;
        try {
            (new SubUnit())->deleteSubUnit($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Sub Unit deleted successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateSubUnitStatus($id)
    {
        $status_code = $status_message = null;
        try {
            (new SubUnit())->updateSubUnitStatus($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Sub Unit status updated successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
