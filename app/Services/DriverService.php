<?php

namespace App\Services;

use App\Traits\FileUploader;
use Illuminate\Support\Facades\DB;
use App\Models\Drivers;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;

class DriverService
{
    /** Plain password assigned when a DSR login user is created (not stored reversibly in DB). */
    public const DEFAULT_DSR_PLAIN_PASSWORD = '123456789';

    public static function defaultDsrPlainPassword(): string
    {
        return self::DEFAULT_DSR_PLAIN_PASSWORD;
    }

    public function getDriverList($search = [], $is_paginate = true, $is_relation = false)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Drivers())->getrDriverList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'DSR list fetched';
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

            // Create login user for this DSR (Spatie role name remains "Driver" in DB/seeders)
            $role = Role::where('name', 'Driver')->where('guard_name', 'web')->first();
            if (! $role) {
                throw new \RuntimeException('Spatie role "Driver" is missing. Run database seeders.');
            }

            $placeholderEmail = 'dsr' . $driver->id . '@example.com';
            $user = User::create([
                'role_id' => $role->id,
                'name' => $request->name,
                'phone' => $request->phone ?? null,
                'email' => $placeholderEmail,
                'type' => 1,
                'driver_id' => $driver->id,
                'password' => self::DEFAULT_DSR_PLAIN_PASSWORD,
            ]);

            $user->assignRole('Driver');

            Employee::updateOrCreate(
                ['driver_id' => $driver->id],
                [
                    'name' => $request->name,
                    'email' => $placeholderEmail,
                    'phone' => $request->phone ?? null,
                    'designation' => 'DSR',
                    'salary' => 0,
                    'status' => $request->status == Drivers::STATUS_ACTIVE ? Employee::STATUS_ACTIVE : Employee::STATUS_INACTIVE,
                ]
            );

            DB::commit();

            return [
                ApiService::API_SUCCESS,
                'DSR created successfully',
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

            $placeholderEmail = 'dsr' . $driver->id . '@example.com';

            $user = User::where('driver_id', $id)->first();
            if ($user) {
                $user->update([
                    'name'  => $request->name,
                    'phone' => $request->phone ?? null,
                    'email' => $placeholderEmail,
                ]);
            }

            Employee::updateOrCreate(
                ['driver_id' => $driver->id],
                [
                    'name' => $request->name,
                    'email' => $placeholderEmail,
                    'phone' => $request->phone ?? null,
                    'designation' => 'DSR',
                    'salary' => Employee::where('driver_id', $driver->id)->value('salary') ?? 0,
                    'status' => $request->status == Drivers::STATUS_ACTIVE ? Employee::STATUS_ACTIVE : Employee::STATUS_INACTIVE,
                ]
            );

            DB::commit();

            return [
                ApiService::API_SUCCESS,
                'DSR updated successfully.',
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
            DB::beginTransaction();

            [$status_code, $status_message, $driver] = self::getDriverById($id);
            if ($status_code !== ApiService::API_SUCCESS || empty($driver)) {
                DB::rollBack();

                return [
                    ApiService::API_SERVER_ERROR,
                    $status_message ?: 'DSR not found.',
                ];
            }

            $user = User::where('driver_id', $id)->first();
            if ($user) {
                $user->syncRoles([]);
                if (! empty($user->image)) {
                    FileUploader::unlinkfile($user->image);
                }
                $user->delete();
            }

            Employee::where('driver_id', $id)->delete();
            Drivers::where('id', $id)->delete();

            DB::commit();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('DSR deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
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
            $status_message = 'DSR status changed successfully.';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
