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
            41 => 
            array (
                'id' => 68,
                'name' => 'Item create',
                'guard_name' => 'web',
                'parent' => 'Item',
                'created_at' => '2025-12-14 18:25:52',
                'updated_at' => '2025-12-14 18:25:52',
            ),
            42 => 
            array (
                'id' => 69,
                'name' => 'Item view',
                'guard_name' => 'web',
                'parent' => 'Item',
                'created_at' => '2025-12-14 18:25:52',
                'updated_at' => '2025-12-14 18:25:52',
            ),
            43 => 
            array (
                'id' => 70,
                'name' => 'Item edit',
                'guard_name' => 'web',
                'parent' => 'Item',
                'created_at' => '2025-12-14 18:25:52',
                'updated_at' => '2025-12-14 18:25:52',
            ),
            44 => 
            array (
                'id' => 71,
                'name' => 'Item destroy',
                'guard_name' => 'web',
                'parent' => 'Item',
                'created_at' => '2025-12-14 18:25:52',
                'updated_at' => '2025-12-14 18:25:52',
            ),
            45 => 
            array (
                'id' => 72,
                'name' => 'Item status',
                'guard_name' => 'web',
                'parent' => 'Item',
                'created_at' => '2025-12-14 18:25:52',
                'updated_at' => '2025-12-14 18:25:52',
            ),
            46 => 
            array (
                'id' => 73,
                'name' => 'Item restore',
                'guard_name' => 'web',
                'parent' => 'Item',
                'created_at' => '2025-12-14 18:25:52',
                'updated_at' => '2025-12-14 18:25:52',
            ),
            47 => 
            array (
                'id' => 74,
                'name' => 'Item delete',
                'guard_name' => 'web',
                'parent' => 'Item',
                'created_at' => '2025-12-14 18:25:52',
                'updated_at' => '2025-12-14 18:25:52',
            ),
            48 => 
            array (
                'id' => 75,
                'name' => 'Item print',
                'guard_name' => 'web',
                'parent' => 'Item',
                'created_at' => '2025-12-14 18:25:52',
                'updated_at' => '2025-12-14 18:25:52',
            ),
            49 => 
            array (
                'id' => 76,
                'name' => 'Item show',
                'guard_name' => 'web',
                'parent' => 'Item',
                'created_at' => '2025-12-14 18:25:52',
                'updated_at' => '2025-12-14 18:25:52',
            ),
            50 => 
            array (
                'id' => 77,
                'name' => 'Item trash',
                'guard_name' => 'web',
                'parent' => 'Item',
                'created_at' => '2025-12-14 18:25:52',
                'updated_at' => '2025-12-14 18:25:52',
            ),
            51 => 
            array (
                'id' => 78,
                'name' => 'Category create',
                'guard_name' => 'web',
                'parent' => 'Category',
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            52 => 
            array (
                'id' => 79,
                'name' => 'Category view',
                'guard_name' => 'web',
                'parent' => 'Category',
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            53 => 
            array (
                'id' => 80,
                'name' => 'Category edit',
                'guard_name' => 'web',
                'parent' => 'Category',
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            54 => 
            array (
                'id' => 81,
                'name' => 'Category destroy',
                'guard_name' => 'web',
                'parent' => 'Category',
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            55 => 
            array (
                'id' => 82,
                'name' => 'Category status',
                'guard_name' => 'web',
                'parent' => 'Category',
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            56 => 
            array (
                'id' => 83,
                'name' => 'Category restore',
                'guard_name' => 'web',
                'parent' => 'Category',
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            57 => 
            array (
                'id' => 84,
                'name' => 'Category delete',
                'guard_name' => 'web',
                'parent' => 'Category',
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            58 => 
            array (
                'id' => 85,
                'name' => 'Category print',
                'guard_name' => 'web',
                'parent' => 'Category',
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            59 => 
            array (
                'id' => 86,
                'name' => 'Category show',
                'guard_name' => 'web',
                'parent' => 'Category',
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            60 => 
            array (
                'id' => 87,
                'name' => 'Category trash',
                'guard_name' => 'web',
                'parent' => 'Category',
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            61 => 
            array (
                'id' => 88,
                'name' => 'Brand create',
                'guard_name' => 'web',
                'parent' => 'Brand',
                'created_at' => '2025-12-14 18:29:13',
                'updated_at' => '2025-12-14 18:29:13',
            ),
            62 => 
            array (
                'id' => 89,
                'name' => 'Brand view',
                'guard_name' => 'web',
                'parent' => 'Brand',
                'created_at' => '2025-12-14 18:29:13',
                'updated_at' => '2025-12-14 18:29:13',
            ),
            63 => 
            array (
                'id' => 90,
                'name' => 'Brand edit',
                'guard_name' => 'web',
                'parent' => 'Brand',
                'created_at' => '2025-12-14 18:29:13',
                'updated_at' => '2025-12-14 18:29:13',
            ),
            64 => 
            array (
                'id' => 91,
                'name' => 'Brand destroy',
                'guard_name' => 'web',
                'parent' => 'Brand',
                'created_at' => '2025-12-14 18:29:13',
                'updated_at' => '2025-12-14 18:29:13',
            ),
            65 => 
            array (
                'id' => 92,
                'name' => 'Brand status',
                'guard_name' => 'web',
                'parent' => 'Brand',
                'created_at' => '2025-12-14 18:29:13',
                'updated_at' => '2025-12-14 18:29:13',
            ),
            66 => 
            array (
                'id' => 93,
                'name' => 'Brand restore',
                'guard_name' => 'web',
                'parent' => 'Brand',
                'created_at' => '2025-12-14 18:29:13',
                'updated_at' => '2025-12-14 18:29:13',
            ),
            67 => 
            array (
                'id' => 94,
                'name' => 'Brand delete',
                'guard_name' => 'web',
                'parent' => 'Brand',
                'created_at' => '2025-12-14 18:29:14',
                'updated_at' => '2025-12-14 18:29:14',
            ),
            68 => 
            array (
                'id' => 95,
                'name' => 'Brand print',
                'guard_name' => 'web',
                'parent' => 'Brand',
                'created_at' => '2025-12-14 18:29:14',
                'updated_at' => '2025-12-14 18:29:14',
            ),
            69 => 
            array (
                'id' => 96,
                'name' => 'Brand show',
                'guard_name' => 'web',
                'parent' => 'Brand',
                'created_at' => '2025-12-14 18:29:14',
                'updated_at' => '2025-12-14 18:29:14',
            ),
            70 => 
            array (
                'id' => 97,
                'name' => 'Brand trash',
                'guard_name' => 'web',
                'parent' => 'Brand',
                'created_at' => '2025-12-14 18:29:14',
                'updated_at' => '2025-12-14 18:29:14',
            ),
        ));
        
        
    }
}