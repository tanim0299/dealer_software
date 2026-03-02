<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $now = now();

            $section = DB::table('menu_sections')->where('name', 'Income Expense')->first();
            if (!$section) {
                $sectionId = DB::table('menu_sections')->insertGetId([
                    'sl' => (int) DB::table('menu_sections')->max('sl') + 1,
                    'name' => 'Human Resource',
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $sectionId = $section->id;
            }

            $parentMenuId = DB::table('menus')->where('name', 'Employee Management')->value('id');
            if (!$parentMenuId) {
                $parentMenuId = DB::table('menus')->insertGetId([
                    'sl' => (int) DB::table('menus')->max('sl') + 1,
                    'menu_section_id' => $sectionId,
                    'parent_id' => null,
                    'name' => 'Employee Management',
                    'system_name' => null,
                    'route' => null,
                    'slug' => null,
                    'icon' => 'fa fa-users',
                    'type' => 1,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $menus = [
                [
                    'name' => 'Employee',
                    'system_name' => 'Employee',
                    'route' => 'employee',
                ],
                [
                    'name' => 'Salary Deposit',
                    'system_name' => 'Employee Salary Deposit',
                    'route' => 'employee_salary_deposit',
                ],
                [
                    'name' => 'Salary Withdraw',
                    'system_name' => 'Employee Salary Withdraw',
                    'route' => 'employee_salary_withdraw',
                ],
            ];

            $permissions = ['create', 'view', 'edit', 'destroy'];

            foreach ($menus as $menu) {
                $exists = DB::table('menus')->where('route', $menu['route'])->exists();

                if (!$exists) {
                    DB::table('menus')->insert([
                        'sl' => (int) DB::table('menus')->max('sl') + 1,
                        'menu_section_id' => $sectionId,
                        'parent_id' => $parentMenuId,
                        'name' => $menu['name'],
                        'system_name' => $menu['system_name'],
                        'route' => $menu['route'],
                        'slug' => 'index',
                        'icon' => null,
                        'type' => 2,
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                foreach ($permissions as $permission) {
                    $permissionName = $menu['system_name'] . ' ' . $permission;

                    $permissionId = DB::table('permissions')->where('name', $permissionName)->value('id');
                    if (!$permissionId) {
                        $permissionId = DB::table('permissions')->insertGetId([
                            'name' => $permissionName,
                            'guard_name' => 'web',
                            'parent' => $menu['system_name'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }

                    $assigned = DB::table('role_has_permissions')
                        ->where('permission_id', $permissionId)
                        ->where('role_id', 1)
                        ->exists();

                    if (!$assigned) {
                        DB::table('role_has_permissions')->insert([
                            'permission_id' => $permissionId,
                            'role_id' => 1,
                        ]);
                    }
                }
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            $parents = ['Employee', 'Employee Salary Deposit', 'Employee Salary Withdraw'];

            $permissionIds = DB::table('permissions')->whereIn('parent', $parents)->pluck('id');

            if ($permissionIds->isNotEmpty()) {
                DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
            }

            DB::table('permissions')->whereIn('parent', $parents)->delete();

            DB::table('menus')->whereIn('route', ['employee', 'employee_salary_deposit', 'employee_salary_withdraw'])->delete();
            DB::table('menus')->where('name', 'Employee Management')->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
