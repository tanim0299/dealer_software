<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Drivers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DriverService
{

    public function getDriverList($search = [], $is_paginate = true, $is_relation = false)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Drivers())->getrDriverList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Driver List Fetched';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        } finally {
            return [$status_code, $status_message, $response];
        }
    }

    public function storeDriver($request)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::driverStoreRules();
        $validation = RequestRules::validate($request->all(), $rules, $messages);

        if ($validation[0] == ApiService::Api_VALIDATION_ERROR) {
            $status_code    = $validation[0];
            $status_message = $validation[1];
            $error_message  = $validation[2];
        } else {
            try {
                DB::beginTransaction();

                $driver = new Drivers();
                $driver->createDriver($request);

                User::create([
                    'name'      => $request->name,
                    'phone'     => $request->phone ?? null,
                    'email'     => 'driver' . $driver->id . '@example.com',
                    'type'      => 1,
                    'driver_id' => $driver->id,
                    'password'   => Hash::make('123456789'),
                ]);

                DB::commit();

                $status_code    = ApiService::API_SUCCESS;
                $status_message = 'Driver Created';
                $error_message  = null;
            } catch (\Throwable $th) {
                DB::rollBack();
                $status_code    = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message  = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getDriverById($id)
    {
        $status_code = $status_message = $driver = '';
        try {
            $driver = (new Drivers())->getDriverById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $driver];
    }

    public function updateDriver($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::driverStoreRules($request->all(), $id);
        $validation = RequestRules::validate($request->all(), $rules, $messages);

        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code    = $validation[0];
            $status_message = $validation[1];
            $error_message  = $validation[2];
        } else {
            try {
                DB::beginTransaction();

                $driver = Drivers::findOrFail($id);
                $driver->updateDriver($request);

                $user = User::where('driver_id', $id)->first();
                if ($user) {
                    $user->update([
                        'name'      => $request->name,
                        'phone'     => $request->phone ?? null,
                        'email'     => 'driver' . $driver->id . '@example.com',
                    ]);
                }

                DB::commit();

                $status_code    = ApiService::API_SUCCESS;
                $status_message = "Driver updated successfully.";
                $error_message  = null;
            } catch (\Throwable $th) {
                DB::rollBack();
                $status_code    = ApiService::API_SERVER_ERROR;
                $status_message = 'Something went wrong.';
                $error_message  = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteDriver($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getDriverById($id);
            Drivers::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Driver deleted successfully.');
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateDriverStatus($request)
    {
        $status_code = $status_message = null;
        try {
            (new Drivers())->updateStatus($request);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Driver status changed successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
