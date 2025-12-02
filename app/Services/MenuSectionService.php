<?php
namespace App\Services;

use App\Models\MenuSection;
use Symfony\Component\HttpFoundation\Request;

class MenuSectionService
{
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
}