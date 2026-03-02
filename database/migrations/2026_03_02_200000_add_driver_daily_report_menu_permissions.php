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

            $parentMenuId = DB::table('menus')
                ->where('name', 'Report Management')
                ->where('type', 1)
                ->value('id');

            if (!$parentMenuId) {
                $parentMenuId = DB::table('menus')->insertGetId([
                    'sl' => ((int) DB::table('menus')->max('sl')) + 1,
                    'menu_section_id' => $sectionId,
                    'parent_id' => null,
                    'name' => 'Report Management',
                    'system_name' => null,
                    'route' => null,
                    'slug' => null,
                    'icon' => 'fa fa-file-alt',
                    'type' => 1,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $menuExists = DB::table('menus')
                ->where('route', 'driver_closing')
                ->where('slug', 'index')
                ->exists();

            if (!$menuExists) {
                DB::table('menus')->insert([
                    'sl' => ((int) DB::table('menus')->max('sl')) + 1,
                    'menu_section_id' => $sectionId,
                    'parent_id' => $parentMenuId,
                    'name' => 'Driver Daily Report',
                    'system_name' => 'Driver Daily Report',
                    'route' => 'driver_closing',
                    'slug' => 'index',
                    'icon' => null,
                    'type' => 2,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $permissions = ['view', 'create'];

            foreach ($permissions as $permission) {
                $permissionName = 'Driver Daily Report ' . $permission;

                $permissionId = DB::table('permissions')->where('name', $permissionName)->value('id');
                if (!$permissionId) {
                    $permissionId = DB::table('permissions')->insertGetId([
                        'name' => $permissionName,
                        'guard_name' => 'web',
                        'parent' => 'Driver Daily Report',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $isAssigned = DB::table('role_has_permissions')
                    ->where('permission_id', $permissionId)
                    ->where('role_id', 1)
                    ->exists();

                if (!$isAssigned) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permissionId,
                        'role_id' => 1,
                    ]);
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
            $permissionIds = DB::table('permissions')
                ->where('parent', 'Driver Daily Report')
                ->pluck('id');

            if ($permissionIds->isNotEmpty()) {
                DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
                DB::table('permissions')->whereIn('id', $permissionIds)->delete();
            }

            DB::table('menus')
                ->where('system_name', 'Driver Daily Report')
                ->where('route', 'driver_closing')
                ->where('slug', 'index')
                ->delete();

            $parent = DB::table('menus')->where('name', 'Report Management')->where('type', 1)->first();
            if ($parent) {
                $hasChildren = DB::table('menus')->where('parent_id', $parent->id)->exists();
                if (!$hasChildren) {
                    DB::table('menus')->where('id', $parent->id)->delete();
                }
            }

            $section = DB::table('menu_sections')->where('name', 'Reports')->first();
            if ($section) {
                $hasMenus = DB::table('menus')->where('menu_section_id', $section->id)->exists();
                if (!$hasMenus) {
                    DB::table('menu_sections')->where('id', $section->id)->delete();
                }
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
