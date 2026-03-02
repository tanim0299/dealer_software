<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService
{
    public function getEmployeeList($search = [], $is_paginate = true)
    {
        $status_code = $status_message = $response = '';

        try {
            $response = (new Employee())->getEmployeeList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Employees Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function getEmployeeById($id)
    {
        $status_code = $status_message = $response = '';

        try {
            $response = (new Employee())->find($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Employee Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeEmployee($request)
    {
        $status_code = $status_message = $error_message = '';

        [$rules, $messages] = RequestRules::employeeRules($request);
        $validation = RequestRules::validate($request->all(), $rules, $messages);

        if ($validation[0] == ApiService::Api_VALIDATION_ERROR) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        } else {
            try {
                (new Employee())->storeEmployee($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Employee Created';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function updateEmployeeById($request, $id)
    {
        $status_code = $status_message = $error_message = '';

        [$rules, $messages] = RequestRules::employeeRules($request, $id);
        $validation = RequestRules::validate($request->all(), $rules, $messages);

        if ($validation[0] == ApiService::Api_VALIDATION_ERROR) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        } else {
            try {
                (new Employee())->updateEmployeeById($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Employee Updated';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteEmployeeById($id)
    {
        $status_code = $status_message = '';

        try {
            (new Employee())->deleteEmployeeById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Employee Deleted';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
