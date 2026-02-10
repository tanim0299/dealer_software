<?php
namespace App\Services;

use App\Models\IncomeEntry;

class IncomeEntryService
{
    public function IncomeEntryList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new IncomeEntry())->IncomeEntryList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeIncomeEntry($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::IncomeEntryRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new IncomeEntry())->createIncomeEntry($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Income Entry created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getIncomeEntryById($id)
    {
        $status_code = $status_message = $IncomeEntry = '';
        try {
            $IncomeEntry = (new IncomeEntry())->getIncomeEntryById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $IncomeEntry];
    }

    public function updateIncomeEntry($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::IncomeEntryRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new IncomeEntry())->updateIncomeEntry($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Income Entry updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteIncomeEntry($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getIncomeEntryById($id);
            IncomeEntry::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Income Entry deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateIncomeEntryStatus($id)
    {
        $status_code = $status_message = null;
        try {
            (new IncomeEntry())->updateStatus($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Income Entry status updated successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
