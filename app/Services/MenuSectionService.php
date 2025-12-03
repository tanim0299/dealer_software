<?php
namespace App\Services;

use App\Models\MenuSection;
use Symfony\Component\HttpFoundation\Request;

class MenuSectionService
{
    public function MenuSectionList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new MenuSection())->MenuSectionList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeMenuSection($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::menuSectionRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new MenuSection())->createMenuSection($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Menu Section created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getMenuSectionById($id)
    {
        $status_code = $status_message = $menu_section = '';
        try {
            $menu_section = (new MenuSection())->getMenuSectionById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code, $status_message, $menu_section];
    }

    public function updateMenuSection($request, $id)
    {
        $status_code = $status_message = $error_message = null;

        [$rules, $messages] = RequestRules::menuSectionRules($request->all(), $id);
        $validation = RequestRules::validate($request->all(), $rules, $messages);

        if ($validation[0] !== ApiService::API_SUCCESS) {
            return [$validation[0], $validation[1], $validation[2]];
        }

        try {
            $updated = (new MenuSection())->updateMenuSection($request, $id);

            if ($updated) {
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Menu Section updated successfully.";
                $error_message = null;
            } else {
                $status_code = ApiService::API_NOT_FOUND;
                $status_message = "Menu Section not found.";
                $error_message = ["Menu Section not found."];
            }

        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
            $error_message = [$th->getMessage()];
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteMenuSection($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getMenuSectionById($id);
            MenuSection::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Menu Section deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateMenuSectionStatus($id)
    {
        try {
            MenuSection::updateStatus($id);

            return [
                ApiService::API_SUCCESS,
                'Status Updated Successfully'
            ];
        } catch (\Throwable $th) {
            return [
                ApiService::API_SERVER_ERROR,
                $th->getMessage()
            ];
        }
    }

}
