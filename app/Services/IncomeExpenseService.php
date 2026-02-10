<?php
namespace App\Services;

use App\Models\IncomeExpenseTitle;
use Symfony\Component\HttpFoundation\Request;

class IncomeExpenseService
{
    public function IncomeExpenseTitleList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new IncomeExpenseTitle())->IncomeExpenseTitleList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeIncomeExpenseTitle($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::IncomeExpenseTitleRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new IncomeExpenseTitle())->createIncomeExpenseTitle($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Income Expense Title created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getIncomeExpenseTitleById($id)
    {
        $status_code = $status_message = $title = '';
        try {
            $title = (new IncomeExpenseTitle())->getIncomeExpenseTitleById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $title];
    }

    public function updateIncomeExpenseTitle($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::IncomeExpenseTitleRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new IncomeExpenseTitle())->updateIncomeExpenseTitle($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Income Expense Title updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteIncomeExpenseTitle($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getIncomeExpenseTitleById($id);
            IncomeExpenseTitle::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Income Expense Title deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateIncomeExpenseTitleStatus($request)
    {
        $status_code = $status_message = null;
        try {
            (new IncomeExpenseTitle())->updateStatus($request);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Income Expense Title status changed successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
