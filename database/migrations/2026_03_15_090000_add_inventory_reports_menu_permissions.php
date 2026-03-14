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

            $menus = [
                ['name' => 'Reports Dashboard', 'system_name' => 'Reports Dashboard', 'route' => 'reports', 'slug' => 'index'],
                ['name' => 'Sales Report', 'system_name' => 'Sales Report', 'route' => 'sales_report', 'slug' => 'index'],
                ['name' => 'Purchase Report', 'system_name' => 'Purchase Report', 'route' => 'purchase_report', 'slug' => 'index'],
                ['name' => 'Cash Report', 'system_name' => 'Cash Report', 'route' => 'cash_report', 'slug' => 'index'],
                ['name' => 'Stock Report', 'system_name' => 'Stock Report', 'route' => 'stock_report', 'slug' => 'index'],
                ['name' => 'Sales Return Report', 'system_name' => 'Sales Return Report', 'route' => 'sales_return_report', 'slug' => 'index'],
                ['name' => 'Purchase Return Report', 'system_name' => 'Purchase Return Report', 'route' => 'purchase_return_report', 'slug' => 'index'],
                ['name' => 'Income Report', 'system_name' => 'Income Report', 'route' => 'income_report', 'slug' => 'index'],
                ['name' => 'Expense Report', 'system_name' => 'Expense Report', 'route' => 'expense_report', 'slug' => 'index'],
            ];

            foreach ($menus as $menu) {
                $exists = DB::table('menus')
                    ->where('system_name', $menu['system_name'])
                    ->where('route', $menu['route'])
                    ->where('slug', $menu['slug'])
                    ->exists();

                if (!$exists) {
                    DB::table('menus')->insert([
                        'sl' => ((int) DB::table('menus')->max('sl')) + 1,
                        'menu_section_id' => $sectionId,
                        'parent_id' => $parentId,
                        'name' => $menu['name'],
                        'system_name' => $menu['system_name'],
                        'route' => $menu['route'],
                        'slug' => $menu['slug'],
                        'icon' => null,
                        'type' => 2,
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $actions = $menu['system_name'] === 'Reports Dashboard' ? ['view'] : ['view', 'create'];
                foreach ($actions as $action) {
                    $permissionName = $menu['system_name'] . ' ' . $action;
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

    public function down(): void
    {
        DB::transaction(function () {
            $systemNames = [
                'Reports Dashboard',
                'Sales Report',
                'Purchase Report',
                'Cash Report',
                'Stock Report',
                'Sales Return Report',
                'Purchase Return Report',
                'Income Report',
                'Expense Report',
            ];

            $permissionIds = DB::table('permissions')->whereIn('parent', $systemNames)->pluck('id');
            if ($permissionIds->isNotEmpty()) {
                DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
                DB::table('permissions')->whereIn('id', $permissionIds)->delete();
            }

            DB::table('menus')->whereIn('system_name', $systemNames)->delete();

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
