<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $now = now();

            $section = DB::table('menu_sections')->where('name', 'Reports')->first();
            if (!$section) {
                $sectionId = DB::table('menu_sections')->insertGetId([
                    'sl' => ((int) DB::table('menu_sections')->max('sl')) + 1,
                    'name' => 'Reports',
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $sectionId = $section->id;
            }

            $parent = DB::table('menus')
                ->where('name', 'Report Management')
                ->where('type', 1)
                ->first();

            if (!$parent) {
                $parentId = DB::table('menus')->insertGetId([
                    'sl' => ((int) DB::table('menus')->max('sl')) + 1,
                    'menu_section_id' => $sectionId,
                    'parent_id' => null,
                    'name' => 'Report Management',
                    'system_name' => null,
                    'route' => null,
                    'slug' => null,
                    'icon' => 'fas fa-chart-line',
                    'type' => 1,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $parentId = $parent->id;
            }

            $exists = DB::table('menus')
                ->where('system_name', 'Sales Profit Report')
                ->where('route', 'sales_profit_report')
                ->where('slug', 'index')
                ->exists();

            if (!$exists) {
                DB::table('menus')->insert([
                    'sl' => ((int) DB::table('menus')->max('sl')) + 1,
                    'menu_section_id' => $sectionId,
                    'parent_id' => $parentId,
                    'name' => 'Sales Profit Report',
                    'system_name' => 'Sales Profit Report',
                    'route' => 'sales_profit_report',
                    'slug' => 'index',
                    'icon' => null,
                    'type' => 2,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach (['view', 'create'] as $action) {
                $permissionName = 'Sales Profit Report ' . $action;
                $permissionId = DB::table('permissions')->where('name', $permissionName)->value('id');

                if (!$permissionId) {
                    $permissionId = DB::table('permissions')->insertGetId([
                        'name' => $permissionName,
                        'guard_name' => 'web',
                        'parent' => 'Sales Profit Report',
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
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::transaction(function () {
            $permissionIds = DB::table('permissions')
                ->where('parent', 'Sales Profit Report')
                ->pluck('id');

            if ($permissionIds->isNotEmpty()) {
                DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
                DB::table('permissions')->whereIn('id', $permissionIds)->delete();
            }

            DB::table('menus')->where('system_name', 'Sales Profit Report')->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
