<?php
namespace App\Services;

use App\Models\User;

class UserService {
    public function getUserList($search = [], $is_paginate = true, $is_relation = true)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new User())->getUserList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'User Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code , $status_message, $response];
    }

    public function storeUser($request)
    {
        $status_code = $status_message = $error_message = $response = '';
        [$rules, $messages] = RequestRules::userRules($request);
        $validaiton = RequestRules::validate($request->all(), $rules, $messages);
        if($validaiton[0] == ApiService::Api_VALIDATION_ERROR)
        {
            $status_code = $validaiton[0];
            $status_message = $validaiton[1];
            $error_message = $validaiton[2];
        }
        else
        {
            try {
                $response = (new User())->createUser($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'User Created';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            }
        }

        return [$status_code, $status_message, $error_message, $response];

    }

    public function getUserById($id)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new User())->find($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'User Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code, $status_message, $response];
    }

    public function updateUserById($request, $id)
    {
        $status_code = $status_message = $error_message = $response = '';
        [$rules, $messages] = RequestRules::userRules($request, $id);
        $validaiton = RequestRules::validate($request->all(), $rules, $messages);
        if($validaiton[0] == ApiService::Api_VALIDATION_ERROR)
        {
            $status_code = $validaiton[0];
            $status_message = $validaiton[1];
            $error_message = $validaiton[2];
        }
        else
        {
            try {
                $response = (new User())->updateUserById($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'User Updated';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            }
        }

        return [$status_code, $status_message, $error_message, $response];
    }

    public function deleteUserById($id)
    {
        $status_code = $status_message = '';
        try {
            (new User())->deleteUserById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'User Deleted';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code, $status_message];
    }
}