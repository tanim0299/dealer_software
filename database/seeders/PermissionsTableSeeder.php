<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 27,
                'name' => 'Dashboard view',
                'guard_name' => 'web',
                'parent' => 'Dashboard',
                'created_at' => '2025-12-08 16:45:13',
                'updated_at' => '2025-12-08 16:45:13',
            ),
            1 => 
            array (
                'id' => 28,
                'name' => 'Menu Section create',
                'guard_name' => 'web',
                'parent' => 'Menu Section',
                'created_at' => '2025-12-08 16:46:53',
                'updated_at' => '2025-12-08 16:46:53',
            ),
            2 => 
            array (
                'id' => 29,
                'name' => 'Menu Section view',
                'guard_name' => 'web',
                'parent' => 'Menu Section',
                'created_at' => '2025-12-08 16:46:53',
                'updated_at' => '2025-12-08 16:46:53',
            ),
            3 => 
            array (
                'id' => 30,
                'name' => 'Menu Section edit',
                'guard_name' => 'web',
                'parent' => 'Menu Section',
                'created_at' => '2025-12-08 16:46:53',
                'updated_at' => '2025-12-08 16:46:53',
            ),
            4 => 
            array (
                'id' => 31,
                'name' => 'Menu Section destroy',
                'guard_name' => 'web',
                'parent' => 'Menu Section',
                'created_at' => '2025-12-08 16:46:53',
                'updated_at' => '2025-12-08 16:46:53',
            ),
            5 => 
            array (
                'id' => 32,
                'name' => 'Menu Section status',
                'guard_name' => 'web',
                'parent' => 'Menu Section',
                'created_at' => '2025-12-08 16:46:53',
                'updated_at' => '2025-12-08 16:46:53',
            ),
            6 => 
            array (
                'id' => 33,
                'name' => 'Menu Section restore',
                'guard_name' => 'web',
                'parent' => 'Menu Section',
                'created_at' => '2025-12-08 16:46:53',
                'updated_at' => '2025-12-08 16:46:53',
            ),
            7 => 
            array (
                'id' => 34,
                'name' => 'Menu Section delete',
                'guard_name' => 'web',
                'parent' => 'Menu Section',
                'created_at' => '2025-12-08 16:46:53',
                'updated_at' => '2025-12-08 16:46:53',
            ),
            8 => 
            array (
                'id' => 35,
                'name' => 'Menu Section print',
                'guard_name' => 'web',
                'parent' => 'Menu Section',
                'created_at' => '2025-12-08 16:46:53',
                'updated_at' => '2025-12-08 16:46:53',
            ),
            9 => 
            array (
                'id' => 36,
                'name' => 'Menu Section show',
                'guard_name' => 'web',
                'parent' => 'Menu Section',
                'created_at' => '2025-12-08 16:46:53',
                'updated_at' => '2025-12-08 16:46:53',
            ),
            10 => 
            array (
                'id' => 37,
                'name' => 'Menu Section trash',
                'guard_name' => 'web',
                'parent' => 'Menu Section',
                'created_at' => '2025-12-08 16:46:53',
                'updated_at' => '2025-12-08 16:46:53',
            ),
            11 => 
            array (
                'id' => 38,
                'name' => 'Menu create',
                'guard_name' => 'web',
                'parent' => 'Menu',
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            12 => 
            array (
                'id' => 39,
                'name' => 'Menu view',
                'guard_name' => 'web',
                'parent' => 'Menu',
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            13 => 
            array (
                'id' => 40,
                'name' => 'Menu edit',
                'guard_name' => 'web',
                'parent' => 'Menu',
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            14 => 
            array (
                'id' => 41,
                'name' => 'Menu destroy',
                'guard_name' => 'web',
                'parent' => 'Menu',
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            15 => 
            array (
                'id' => 42,
                'name' => 'Menu status',
                'guard_name' => 'web',
                'parent' => 'Menu',
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            16 => 
            array (
                'id' => 43,
                'name' => 'Menu restore',
                'guard_name' => 'web',
                'parent' => 'Menu',
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            17 => 
            array (
                'id' => 44,
                'name' => 'Menu delete',
                'guard_name' => 'web',
                'parent' => 'Menu',
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            18 => 
            array (
                'id' => 45,
                'name' => 'Menu print',
                'guard_name' => 'web',
                'parent' => 'Menu',
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            19 => 
            array (
                'id' => 46,
                'name' => 'Menu show',
                'guard_name' => 'web',
                'parent' => 'Menu',
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            20 => 
            array (
                'id' => 47,
                'name' => 'Menu trash',
                'guard_name' => 'web',
                'parent' => 'Menu',
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            21 => 
            array (
                'id' => 48,
                'name' => 'Role create',
                'guard_name' => 'web',
                'parent' => 'Role',
                'created_at' => '2025-12-08 17:11:03',
                'updated_at' => '2025-12-08 17:11:03',
            ),
            22 => 
            array (
                'id' => 49,
                'name' => 'Role view',
                'guard_name' => 'web',
                'parent' => 'Role',
                'created_at' => '2025-12-08 17:11:03',
                'updated_at' => '2025-12-08 17:11:03',
            ),
            23 => 
            array (
                'id' => 50,
                'name' => 'Role edit',
                'guard_name' => 'web',
                'parent' => 'Role',
                'created_at' => '2025-12-08 17:11:03',
                'updated_at' => '2025-12-08 17:11:03',
            ),
            24 => 
            array (
                'id' => 51,
                'name' => 'Role destroy',
                'guard_name' => 'web',
                'parent' => 'Role',
                'created_at' => '2025-12-08 17:11:03',
                'updated_at' => '2025-12-08 17:11:03',
            ),
            25 => 
            array (
                'id' => 52,
                'name' => 'Role status',
                'guard_name' => 'web',
                'parent' => 'Role',
                'created_at' => '2025-12-08 17:11:03',
                'updated_at' => '2025-12-08 17:11:03',
            ),
            26 => 
            array (
                'id' => 53,
                'name' => 'Role restore',
                'guard_name' => 'web',
                'parent' => 'Role',
                'created_at' => '2025-12-08 17:11:04',
                'updated_at' => '2025-12-08 17:11:04',
            ),
            27 => 
            array (
                'id' => 54,
                'name' => 'Role delete',
                'guard_name' => 'web',
                'parent' => 'Role',
                'created_at' => '2025-12-08 17:11:04',
                'updated_at' => '2025-12-08 17:11:04',
            ),
            28 => 
            array (
                'id' => 55,
                'name' => 'Role print',
                'guard_name' => 'web',
                'parent' => 'Role',
                'created_at' => '2025-12-08 17:11:04',
                'updated_at' => '2025-12-08 17:11:04',
            ),
            29 => 
            array (
                'id' => 56,
                'name' => 'Role show',
                'guard_name' => 'web',
                'parent' => 'Role',
                'created_at' => '2025-12-08 17:11:04',
                'updated_at' => '2025-12-08 17:11:04',
            ),
            30 => 
            array (
                'id' => 57,
                'name' => 'Role trash',
                'guard_name' => 'web',
                'parent' => 'Role',
                'created_at' => '2025-12-08 17:11:04',
                'updated_at' => '2025-12-08 17:11:04',
            ),
            31 => 
            array (
                'id' => 58,
                'name' => 'User create',
                'guard_name' => 'web',
                'parent' => 'User',
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
            32 => 
            array (
                'id' => 59,
                'name' => 'User view',
                'guard_name' => 'web',
                'parent' => 'User',
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
            33 => 
            array (
                'id' => 60,
                'name' => 'User edit',
                'guard_name' => 'web',
                'parent' => 'User',
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
            34 => 
            array (
                'id' => 61,
                'name' => 'User destroy',
                'guard_name' => 'web',
                'parent' => 'User',
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
            35 => 
            array (
                'id' => 62,
                'name' => 'User status',
                'guard_name' => 'web',
                'parent' => 'User',
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
            36 => 
            array (
                'id' => 63,
                'name' => 'User restore',
                'guard_name' => 'web',
                'parent' => 'User',
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
            37 => 
            array (
                'id' => 64,
                'name' => 'User delete',
                'guard_name' => 'web',
                'parent' => 'User',
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
            38 => 
            array (
                'id' => 65,
                'name' => 'User print',
                'guard_name' => 'web',
                'parent' => 'User',
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
            39 => 
            array (
                'id' => 66,
                'name' => 'User show',
                'guard_name' => 'web',
                'parent' => 'User',
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
            40 => 
            array (
                'id' => 67,
                'name' => 'User trash',
                'guard_name' => 'web',
                'parent' => 'User',
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
        ));
        
        
    }
}