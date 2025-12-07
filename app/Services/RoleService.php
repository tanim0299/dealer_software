<?php
namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as ModelsRole;

class RoleService
{
    public function getRoleList($search = [], $is_paginate = true, $is_relation = true)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Role())->getRoleList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Role list retrieved successfully.';
        } catch (\Throwable $th) {
           $status_code = ApiService::API_SERVER_ERROR;
           $status_message = $th->getMessage();
        }
        return [$status_code, $status_message, $response];
    }

    public function storeRole($request)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::roleRules($request);
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
                (new Role())->createRole($request);
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

    public function getRoleById($id)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Role())->getRoleById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Role Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code , $status_message , $response];
    }

    public function updateRoleById($request, $id)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::roleRules($request, $id);
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
                (new Role())->updateRole($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Role updated successfully.';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteRoleById($id)
    {
        $status_code = $status_message = '';
        try {
            (new Role())->deleteRoleById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Role Removed';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function getPermissionList()
    {
        $permissions = Permission::all();

        return $groupedPermissions = $permissions->groupBy('parent');
    }

    public function assignPermission($request, $role_id)
    {
        try {
            Artisan::call('cache:forget spatie.permission.cache');
            $role = ModelsRole::find($role_id);
            $role->syncPermissions($request->permissions);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Permission Synced';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function getRolesPermisison($id)
    {
        $role = ModelsRole::find($id);

        $permissions = DB::table('role_has_permissions')->where('role_id',$id)->pluck('permission_id')->toArray();

        return $permissions;
    }
}
