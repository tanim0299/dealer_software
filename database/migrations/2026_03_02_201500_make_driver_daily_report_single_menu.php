<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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

            $menu = DB::table('menus')
                ->where('system_name', 'Driver Daily Report')
                ->where('route', 'driver_closing')
                ->first();

            if ($menu) {
                DB::table('menus')
                    ->where('id', $menu->id)
                    ->update([
                        'menu_section_id' => $sectionId,
                        'parent_id' => null,
                        'name' => 'Driver Daily Report',
                        'system_name' => 'Driver Daily Report',
                        'route' => 'driver_closing',
                        'slug' => 'index',
                        'icon' => 'fa fa-file-alt',
                        'type' => 3,
                        'status' => 1,
                        'updated_at' => $now,
                    ]);
            } else {
                DB::table('menus')->insert([
                    'sl' => ((int) DB::table('menus')->max('sl')) + 1,
                    'menu_section_id' => $sectionId,
                    'parent_id' => null,
                    'name' => 'Driver Daily Report',
                    'system_name' => 'Driver Daily Report',
                    'route' => 'driver_closing',
                    'slug' => 'index',
                    'icon' => 'fa fa-file-alt',
                    'type' => 3,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $parent = DB::table('menus')->where('name', 'Report Management')->where('type', 1)->first();
            if ($parent) {
                DB::table('menus')->where('parent_id', $parent->id)->delete();
                DB::table('menus')->where('id', $parent->id)->delete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left minimal: this migration is a data-fix to enforce single standalone menu.
    }
};
