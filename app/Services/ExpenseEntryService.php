<?php
namespace App\Services;

use App\Models\ExpenseEntry;

class ExpenseEntryService
{
    public function ExpenseEntryList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new ExpenseEntry())->ExpenseEntryList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeExpenseEntry($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::ExpenseEntryRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new ExpenseEntry())->createExpenseEntry($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Expense Entry created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getExpenseEntryById($id)
    {
        $status_code = $status_message = $ExpenseEntry = '';
        try {
            $ExpenseEntry = (new ExpenseEntry())->getExpenseEntryById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $ExpenseEntry];
    }

    public function updateExpenseEntry($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::ExpenseEntryRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new ExpenseEntry())->updateExpenseEntry($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Expense Entry updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteExpenseEntry($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getExpenseEntryById($id);
            ExpenseEntry::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Expense Entry deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateExpenseEntryStatus($id)
    {
        $status_code = $status_message = null;
        try {
            (new ExpenseEntry())->updateStatus($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Expense Entry status updated successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
