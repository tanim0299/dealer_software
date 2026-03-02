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
            $menu = DB::table('menus')
                ->where('system_name', 'Driver Daily Report')
                ->first();

            if ($menu) {
                DB::table('menus')
                    ->where('id', $menu->id)
                    ->update([
                        'route' => 'driver_daily_report',
                        'slug' => 'index',
                        'type' => 3,
                        'parent_id' => null,
                        'updated_at' => now(),
                    ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            $menu = DB::table('menus')
                ->where('system_name', 'Driver Daily Report')
                ->first();

            if ($menu) {
                DB::table('menus')
                    ->where('id', $menu->id)
                    ->update([
                        'route' => 'driver_closing',
                        'slug' => 'index',
                        'updated_at' => now(),
                    ]);
            }
        });
    }
};
