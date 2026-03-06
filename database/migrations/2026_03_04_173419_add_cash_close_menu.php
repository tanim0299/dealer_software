<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the next menu section serial number
        $nextSectionSl = (int) DB::table('menu_sections')->max('sl') + 1;
        $nextMenuSl = (int) DB::table('menus')->max('sl') + 1;

        // Create Cash Close menu section
        $sectionId = DB::table('menu_sections')->insertGetId([
            'sl' => $nextSectionSl,
            'name' => 'Cash Management',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create parent menu for Cash Close
        $parentId = DB::table('menus')->insertGetId([
            'sl' => $nextMenuSl,
            'menu_section_id' => $sectionId,
            'parent_id' => null,
            'name' => 'Cash Management',
            'system_name' => null,
            'route' => null,
            'slug' => null,
            'icon' => 'fa fa-money-bill-wave',
            'type' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Cash Close child menu
        DB::table('menus')->insertGetId([
            'sl' => $nextMenuSl + 1,
            'menu_section_id' => $sectionId,
            'parent_id' => $parentId,
            'name' => 'Daily Cash Close',
            'system_name' => 'Daily Cash Close',
            'route' => 'cash_close',
            'slug' => 'index',
            'icon' => null,
            'type' => 2,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create permissions for Cash Close
        $cash_close_permissions = [
            'cash_close.index' => 'View Cash Close',
            'cash_close.create' => 'Create Cash Close',
            'cash_close.store' => 'Store Cash Close',
            'cash_close.show' => 'View Cash Close Details',
            'cash_close.edit' => 'Edit Cash Close',
            'cash_close.update' => 'Update Cash Close',
        ];

        foreach ($cash_close_permissions as $permission => $display_name) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menu_sections')->where('name', 'Cash Management')->delete();
        DB::table('menus')->where('name', 'Cash Management')->delete();
        DB::table('menus')->where('name', 'Daily Cash Close')->delete();
        
        // Remove Cash Close permissions
        $permissions = [
            'cash_close.index', 'cash_close.create', 'cash_close.store',
            'cash_close.show', 'cash_close.edit', 'cash_close.update',
        ];
        
        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};

