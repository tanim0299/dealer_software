<?php
namespace App\Services;

use App\Models\Item;
use Symfony\Component\HttpFoundation\Request;

class ItemService
{
    public function ItemList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Item())->ItemList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeItem($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::itemRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Item())->createItem($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Item created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getItemById($id)
    {
        $status_code = $status_message = $item = '';
        try {
            $item = (new Item())->getItemById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $item];
    }

    public function updateItem($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::itemRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Item())->updateItem($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Item updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteItem($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getItemById($id);
            Item::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Item deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateItemStatus($request)
    {
        $status_code = $status_message = null;
        try {
            (new Item())->updateStatus($request);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Item status changed successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
