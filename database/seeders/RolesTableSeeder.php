<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'index' => 1,
                'name' => 'Super Admin',
                'guard_name' => 'web',
                'created_at' => '2025-12-06 17:22:00',
                'updated_at' => '2025-12-06 17:22:00',
            ),
            1 => 
            array (
                'id' => 3,
                'index' => 2,
                'name' => 'Driver',
                'guard_name' => 'web',
                'created_at' => '2026-02-01 18:22:31',
                'updated_at' => '2026-02-01 18:22:31',
            ),
        ));
        
        
    }
}