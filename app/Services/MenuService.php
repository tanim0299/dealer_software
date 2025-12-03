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
}