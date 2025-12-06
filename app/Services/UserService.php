<?php
namespace App\Services;

use App\Models\User;

class UserService {
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
}