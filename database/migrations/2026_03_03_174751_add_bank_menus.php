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
        // Get the Bank Management menu section ID
        $sectionId = DB::table('menu_sections')->where('name', 'Bank Management')->first()?->id;
        
        if (!$sectionId) {
            // If section doesn't exist, create it
            $sectionId = DB::table('menu_sections')->insertGetId([
                'sl' => 12,
                'name' => 'Bank Management',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create parent menu
        $parentId = DB::table('menus')->insertGetId([
            'sl' => 44,
            'menu_section_id' => $sectionId,
            'parent_id' => null,
            'name' => 'Bank Management',
            'system_name' => null,
            'route' => null,
            'slug' => null,
            'icon' => 'fa fa-bank',
            'type' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Bank Account child menu
        DB::table('menus')->insertGetId([
            'sl' => 45,
            'menu_section_id' => $sectionId,
            'parent_id' => $parentId,
            'name' => 'Bank Account',
            'system_name' => 'Bank Account',
            'route' => 'bank_account',
            'slug' => 'index',
            'icon' => null,
            'type' => 2,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Bank Transaction child menu
        DB::table('menus')->insertGetId([
            'sl' => 46,
            'menu_section_id' => $sectionId,
            'parent_id' => $parentId,
            'name' => 'Bank Transaction',
            'system_name' => 'Bank Transaction',
            'route' => 'bank_transaction',
            'slug' => 'index',
            'icon' => null,
            'type' => 2,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menus')->where('name', 'Bank Management')->delete();
        DB::table('menus')->where('name', 'Bank Account')->delete();
        DB::table('menus')->where('name', 'Bank Transaction')->delete();
    }
};
