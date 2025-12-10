<?php
namespace App\Services;

use App\Models\WebsiteSettings;
use Symfony\Component\HttpFoundation\Request;

class GlobalSetting
{
    public function getWebsiteSettings() : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new WebsiteSettings())->first();
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeWebsiteSettings($request)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::globalsettingsRules($request);
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
                (new WebsiteSettings())->createWebsiteSettings($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Role created successfully.';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];

    }
}
