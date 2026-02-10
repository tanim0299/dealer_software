<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Drivers;
use App\Models\Role;
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

            return [
                $validation[0],
                $validation[1],
                $validation[2]
            ];
        }

        try {
            DB::beginTransaction();

            $driver = new Drivers();
            $driver = $driver->createDriver($request);

            $driver->areas()->sync($request->area_ids);

            // Create User
            $role = Role::where('name','Driver')->first();
            $user = User::create([
                'role_id' => $role->id ?? 2,
                'name'      => $request->name,
                'phone'     => $request->phone ?? null,
                'email'     => 'driver' . $driver->id . '@example.com',
                'type'      => 1,
                'driver_id' => $driver->id,
                'password'  => Hash::make('123456789'),
            ]);

            $user->assignRole('Driver');

            DB::commit();

            return [
                ApiService::API_SUCCESS,
                'Driver Created Successfully',
                null
            ];
        } catch (\Throwable $th) {

            DB::rollBack();

            return [
                ApiService::API_SERVER_ERROR,
                $th->getMessage(),
                [$th->getMessage()]
            ];
        }
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
            return [
                $validation[0],
                $validation[1],
                $validation[2]
            ];
        }

        try {
            DB::beginTransaction();

            $driver = Drivers::findOrFail($id);
            $driver = $driver->updateDriver($request);

            if ($request->filled('area_ids')) {
                $driver->areas()->sync($request->area_ids);
            }

            $user = User::where('driver_id', $id)->first();
            if ($user) {
                $user->update([
                    'name'  => $request->name,
                    'phone' => $request->phone ?? null,
                    'email' => 'driver' . $driver->id . '@example.com',
                ]);
            }

            DB::commit();

            return [
                ApiService::API_SUCCESS,
                "Driver updated successfully.",
                null
            ];
        } catch (\Throwable $th) {

            DB::rollBack();

            return [
                ApiService::API_SERVER_ERROR,
                'Something went wrong.',
                [$th->getMessage()]
            ];
        }
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
