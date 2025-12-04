<?php 
namespace App\Services;

use App\Models\Menu;

class MenuService
{
    public function getMenuList($search = [], $is_paginate = true, $is_relation = true)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Menu())->getMenuList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Menu list retrieved successfully.';
        } catch (\Throwable $th) {
           $status_code = ApiService::API_SERVER_ERROR;
           $status_message = 'Failed to retrieve menu list.';
        }
        return [$status_code, $status_message, $response];
    }

    public function storeMenu($request)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::menuRules($request);
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
                (new Menu())->createMenuWithPermission($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Menu created successfully.';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
       
    }
}