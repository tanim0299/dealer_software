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
            71 => 
            array (
                'id' => 98,
                'name' => 'Unit create',
                'guard_name' => 'web',
                'parent' => 'Unit',
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            72 => 
            array (
                'id' => 99,
                'name' => 'Unit view',
                'guard_name' => 'web',
                'parent' => 'Unit',
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            73 => 
            array (
                'id' => 100,
                'name' => 'Unit edit',
                'guard_name' => 'web',
                'parent' => 'Unit',
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            74 => 
            array (
                'id' => 101,
                'name' => 'Unit destroy',
                'guard_name' => 'web',
                'parent' => 'Unit',
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            75 => 
            array (
                'id' => 102,
                'name' => 'Unit status',
                'guard_name' => 'web',
                'parent' => 'Unit',
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            76 => 
            array (
                'id' => 103,
                'name' => 'Unit restore',
                'guard_name' => 'web',
                'parent' => 'Unit',
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            77 => 
            array (
                'id' => 104,
                'name' => 'Unit delete',
                'guard_name' => 'web',
                'parent' => 'Unit',
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            78 => 
            array (
                'id' => 105,
                'name' => 'Unit print',
                'guard_name' => 'web',
                'parent' => 'Unit',
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            79 => 
            array (
                'id' => 106,
                'name' => 'Unit show',
                'guard_name' => 'web',
                'parent' => 'Unit',
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            80 => 
            array (
                'id' => 107,
                'name' => 'Unit trash',
                'guard_name' => 'web',
                'parent' => 'Unit',
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            81 => 
            array (
                'id' => 108,
                'name' => 'Sub Unit create',
                'guard_name' => 'web',
                'parent' => 'Sub Unit',
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
            82 => 
            array (
                'id' => 109,
                'name' => 'Sub Unit view',
                'guard_name' => 'web',
                'parent' => 'Sub Unit',
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
            83 => 
            array (
                'id' => 110,
                'name' => 'Sub Unit edit',
                'guard_name' => 'web',
                'parent' => 'Sub Unit',
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
            84 => 
            array (
                'id' => 111,
                'name' => 'Sub Unit destroy',
                'guard_name' => 'web',
                'parent' => 'Sub Unit',
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
            85 => 
            array (
                'id' => 112,
                'name' => 'Sub Unit status',
                'guard_name' => 'web',
                'parent' => 'Sub Unit',
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
            86 => 
            array (
                'id' => 113,
                'name' => 'Sub Unit restore',
                'guard_name' => 'web',
                'parent' => 'Sub Unit',
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
            87 => 
            array (
                'id' => 114,
                'name' => 'Sub Unit delete',
                'guard_name' => 'web',
                'parent' => 'Sub Unit',
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
            88 => 
            array (
                'id' => 115,
                'name' => 'Sub Unit print',
                'guard_name' => 'web',
                'parent' => 'Sub Unit',
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
            89 => 
            array (
                'id' => 116,
                'name' => 'Sub Unit show',
                'guard_name' => 'web',
                'parent' => 'Sub Unit',
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
            90 => 
            array (
                'id' => 117,
                'name' => 'Sub Unit trash',
                'guard_name' => 'web',
                'parent' => 'Sub Unit',
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
            91 => 
            array (
                'id' => 118,
                'name' => 'Product create',
                'guard_name' => 'web',
                'parent' => 'Product',
                'created_at' => '2025-12-16 15:59:51',
                'updated_at' => '2025-12-16 15:59:51',
            ),
            92 => 
            array (
                'id' => 119,
                'name' => 'Product view',
                'guard_name' => 'web',
                'parent' => 'Product',
                'created_at' => '2025-12-16 15:59:51',
                'updated_at' => '2025-12-16 15:59:51',
            ),
            93 => 
            array (
                'id' => 120,
                'name' => 'Product edit',
                'guard_name' => 'web',
                'parent' => 'Product',
                'created_at' => '2025-12-16 15:59:51',
                'updated_at' => '2025-12-16 15:59:51',
            ),
            94 => 
            array (
                'id' => 121,
                'name' => 'Product destroy',
                'guard_name' => 'web',
                'parent' => 'Product',
                'created_at' => '2025-12-16 15:59:51',
                'updated_at' => '2025-12-16 15:59:51',
            ),
            95 => 
            array (
                'id' => 122,
                'name' => 'Product status',
                'guard_name' => 'web',
                'parent' => 'Product',
                'created_at' => '2025-12-16 15:59:51',
                'updated_at' => '2025-12-16 15:59:51',
            ),
            96 => 
            array (
                'id' => 123,
                'name' => 'Product restore',
                'guard_name' => 'web',
                'parent' => 'Product',
                'created_at' => '2025-12-16 15:59:51',
                'updated_at' => '2025-12-16 15:59:51',
            ),
            97 => 
            array (
                'id' => 124,
                'name' => 'Product delete',
                'guard_name' => 'web',
                'parent' => 'Product',
                'created_at' => '2025-12-16 15:59:51',
                'updated_at' => '2025-12-16 15:59:51',
            ),
            98 => 
            array (
                'id' => 125,
                'name' => 'Product print',
                'guard_name' => 'web',
                'parent' => 'Product',
                'created_at' => '2025-12-16 15:59:51',
                'updated_at' => '2025-12-16 15:59:51',
            ),
            99 => 
            array (
                'id' => 126,
                'name' => 'Product show',
                'guard_name' => 'web',
                'parent' => 'Product',
                'created_at' => '2025-12-16 15:59:51',
                'updated_at' => '2025-12-16 15:59:51',
            ),
            100 => 
            array (
                'id' => 127,
                'name' => 'Product trash',
                'guard_name' => 'web',
                'parent' => 'Product',
                'created_at' => '2025-12-16 15:59:51',
                'updated_at' => '2025-12-16 15:59:51',
            ),
            101 => 
            array (
                'id' => 128,
                'name' => 'Supplier create',
                'guard_name' => 'web',
                'parent' => 'Supplier',
                'created_at' => '2025-12-17 16:57:44',
                'updated_at' => '2025-12-17 16:57:44',
            ),
            102 => 
            array (
                'id' => 129,
                'name' => 'Supplier view',
                'guard_name' => 'web',
                'parent' => 'Supplier',
                'created_at' => '2025-12-17 16:57:44',
                'updated_at' => '2025-12-17 16:57:44',
            ),
            103 => 
            array (
                'id' => 130,
                'name' => 'Supplier edit',
                'guard_name' => 'web',
                'parent' => 'Supplier',
                'created_at' => '2025-12-17 16:57:44',
                'updated_at' => '2025-12-17 16:57:44',
            ),
            104 => 
            array (
                'id' => 131,
                'name' => 'Supplier destroy',
                'guard_name' => 'web',
                'parent' => 'Supplier',
                'created_at' => '2025-12-17 16:57:44',
                'updated_at' => '2025-12-17 16:57:44',
            ),
            105 => 
            array (
                'id' => 132,
                'name' => 'Supplier status',
                'guard_name' => 'web',
                'parent' => 'Supplier',
                'created_at' => '2025-12-17 16:57:44',
                'updated_at' => '2025-12-17 16:57:44',
            ),
            106 => 
            array (
                'id' => 133,
                'name' => 'Supplier restore',
                'guard_name' => 'web',
                'parent' => 'Supplier',
                'created_at' => '2025-12-17 16:57:44',
                'updated_at' => '2025-12-17 16:57:44',
            ),
            107 => 
            array (
                'id' => 134,
                'name' => 'Supplier delete',
                'guard_name' => 'web',
                'parent' => 'Supplier',
                'created_at' => '2025-12-17 16:57:44',
                'updated_at' => '2025-12-17 16:57:44',
            ),
            108 => 
            array (
                'id' => 135,
                'name' => 'Supplier print',
                'guard_name' => 'web',
                'parent' => 'Supplier',
                'created_at' => '2025-12-17 16:57:44',
                'updated_at' => '2025-12-17 16:57:44',
            ),
            109 => 
            array (
                'id' => 136,
                'name' => 'Supplier show',
                'guard_name' => 'web',
                'parent' => 'Supplier',
                'created_at' => '2025-12-17 16:57:44',
                'updated_at' => '2025-12-17 16:57:44',
            ),
            110 => 
            array (
                'id' => 137,
                'name' => 'Supplier trash',
                'guard_name' => 'web',
                'parent' => 'Supplier',
                'created_at' => '2025-12-17 16:57:44',
                'updated_at' => '2025-12-17 16:57:44',
            ),
            111 => 
            array (
                'id' => 138,
                'name' => 'Purchase create',
                'guard_name' => 'web',
                'parent' => 'Purchase',
                'created_at' => '2025-12-19 14:23:25',
                'updated_at' => '2025-12-19 14:23:25',
            ),
            112 => 
            array (
                'id' => 139,
                'name' => 'Purchase view',
                'guard_name' => 'web',
                'parent' => 'Purchase',
                'created_at' => '2025-12-19 14:23:25',
                'updated_at' => '2025-12-19 14:23:25',
            ),
            113 => 
            array (
                'id' => 140,
                'name' => 'Purchase edit',
                'guard_name' => 'web',
                'parent' => 'Purchase',
                'created_at' => '2025-12-19 14:23:25',
                'updated_at' => '2025-12-19 14:23:25',
            ),
            114 => 
            array (
                'id' => 141,
                'name' => 'Purchase destroy',
                'guard_name' => 'web',
                'parent' => 'Purchase',
                'created_at' => '2025-12-19 14:23:25',
                'updated_at' => '2025-12-19 14:23:25',
            ),
            115 => 
            array (
                'id' => 142,
                'name' => 'Purchase status',
                'guard_name' => 'web',
                'parent' => 'Purchase',
                'created_at' => '2025-12-19 14:23:25',
                'updated_at' => '2025-12-19 14:23:25',
            ),
            116 => 
            array (
                'id' => 143,
                'name' => 'Purchase restore',
                'guard_name' => 'web',
                'parent' => 'Purchase',
                'created_at' => '2025-12-19 14:23:25',
                'updated_at' => '2025-12-19 14:23:25',
            ),
            117 => 
            array (
                'id' => 144,
                'name' => 'Purchase delete',
                'guard_name' => 'web',
                'parent' => 'Purchase',
                'created_at' => '2025-12-19 14:23:25',
                'updated_at' => '2025-12-19 14:23:25',
            ),
            118 => 
            array (
                'id' => 145,
                'name' => 'Purchase print',
                'guard_name' => 'web',
                'parent' => 'Purchase',
                'created_at' => '2025-12-19 14:23:25',
                'updated_at' => '2025-12-19 14:23:25',
            ),
            119 => 
            array (
                'id' => 146,
                'name' => 'Purchase show',
                'guard_name' => 'web',
                'parent' => 'Purchase',
                'created_at' => '2025-12-19 14:23:25',
                'updated_at' => '2025-12-19 14:23:25',
            ),
            120 => 
            array (
                'id' => 147,
                'name' => 'Purchase trash',
                'guard_name' => 'web',
                'parent' => 'Purchase',
                'created_at' => '2025-12-19 14:23:25',
                'updated_at' => '2025-12-19 14:23:25',
            ),
            121 => 
            array (
                'id' => 148,
                'name' => 'Warehouse Stock view',
                'guard_name' => 'web',
                'parent' => 'Warehouse Stock',
                'created_at' => '2026-01-11 17:28:57',
                'updated_at' => '2026-01-11 17:28:57',
            ),
            122 => 
            array (
                'id' => 149,
                'name' => 'Driver create',
                'guard_name' => 'web',
                'parent' => 'Driver',
                'created_at' => '2026-01-25 16:52:52',
                'updated_at' => '2026-01-25 16:52:52',
            ),
            123 => 
            array (
                'id' => 150,
                'name' => 'Driver view',
                'guard_name' => 'web',
                'parent' => 'Driver',
                'created_at' => '2026-01-25 16:52:53',
                'updated_at' => '2026-01-25 16:52:53',
            ),
            124 => 
            array (
                'id' => 151,
                'name' => 'Driver edit',
                'guard_name' => 'web',
                'parent' => 'Driver',
                'created_at' => '2026-01-25 16:52:53',
                'updated_at' => '2026-01-25 16:52:53',
            ),
            125 => 
            array (
                'id' => 152,
                'name' => 'Driver destroy',
                'guard_name' => 'web',
                'parent' => 'Driver',
                'created_at' => '2026-01-25 16:52:53',
                'updated_at' => '2026-01-25 16:52:53',
            ),
            126 => 
            array (
                'id' => 153,
                'name' => 'Driver status',
                'guard_name' => 'web',
                'parent' => 'Driver',
                'created_at' => '2026-01-25 16:52:53',
                'updated_at' => '2026-01-25 16:52:53',
            ),
            127 => 
            array (
                'id' => 154,
                'name' => 'Driver restore',
                'guard_name' => 'web',
                'parent' => 'Driver',
                'created_at' => '2026-01-25 16:52:53',
                'updated_at' => '2026-01-25 16:52:53',
            ),
            128 => 
            array (
                'id' => 155,
                'name' => 'Driver delete',
                'guard_name' => 'web',
                'parent' => 'Driver',
                'created_at' => '2026-01-25 16:52:53',
                'updated_at' => '2026-01-25 16:52:53',
            ),
            129 => 
            array (
                'id' => 156,
                'name' => 'Driver print',
                'guard_name' => 'web',
                'parent' => 'Driver',
                'created_at' => '2026-01-25 16:52:53',
                'updated_at' => '2026-01-25 16:52:53',
            ),
            130 => 
            array (
                'id' => 157,
                'name' => 'Driver show',
                'guard_name' => 'web',
                'parent' => 'Driver',
                'created_at' => '2026-01-25 16:52:53',
                'updated_at' => '2026-01-25 16:52:53',
            ),
            131 => 
            array (
                'id' => 158,
                'name' => 'Driver trash',
                'guard_name' => 'web',
                'parent' => 'Driver',
                'created_at' => '2026-01-25 16:52:53',
                'updated_at' => '2026-01-25 16:52:53',
            ),
            132 => 
            array (
                'id' => 159,
                'name' => 'Driver Issue create',
                'guard_name' => 'web',
                'parent' => 'Driver Issue',
                'created_at' => '2026-01-27 17:57:46',
                'updated_at' => '2026-01-27 17:57:46',
            ),
            133 => 
            array (
                'id' => 160,
                'name' => 'Driver Issue view',
                'guard_name' => 'web',
                'parent' => 'Driver Issue',
                'created_at' => '2026-01-27 17:57:46',
                'updated_at' => '2026-01-27 17:57:46',
            ),
            134 => 
            array (
                'id' => 161,
                'name' => 'Driver Issue edit',
                'guard_name' => 'web',
                'parent' => 'Driver Issue',
                'created_at' => '2026-01-27 17:57:46',
                'updated_at' => '2026-01-27 17:57:46',
            ),
            135 => 
            array (
                'id' => 162,
                'name' => 'Driver Issue destroy',
                'guard_name' => 'web',
                'parent' => 'Driver Issue',
                'created_at' => '2026-01-27 17:57:46',
                'updated_at' => '2026-01-27 17:57:46',
            ),
            136 => 
            array (
                'id' => 163,
                'name' => 'Driver Issue status',
                'guard_name' => 'web',
                'parent' => 'Driver Issue',
                'created_at' => '2026-01-27 17:57:46',
                'updated_at' => '2026-01-27 17:57:46',
            ),
            137 => 
            array (
                'id' => 164,
                'name' => 'Driver Issue restore',
                'guard_name' => 'web',
                'parent' => 'Driver Issue',
                'created_at' => '2026-01-27 17:57:46',
                'updated_at' => '2026-01-27 17:57:46',
            ),
            138 => 
            array (
                'id' => 165,
                'name' => 'Driver Issue delete',
                'guard_name' => 'web',
                'parent' => 'Driver Issue',
                'created_at' => '2026-01-27 17:57:46',
                'updated_at' => '2026-01-27 17:57:46',
            ),
            139 => 
            array (
                'id' => 166,
                'name' => 'Driver Issue print',
                'guard_name' => 'web',
                'parent' => 'Driver Issue',
                'created_at' => '2026-01-27 17:57:46',
                'updated_at' => '2026-01-27 17:57:46',
            ),
            140 => 
            array (
                'id' => 167,
                'name' => 'Driver Issue show',
                'guard_name' => 'web',
                'parent' => 'Driver Issue',
                'created_at' => '2026-01-27 17:57:46',
                'updated_at' => '2026-01-27 17:57:46',
            ),
            141 => 
            array (
                'id' => 168,
                'name' => 'Driver Issue trash',
                'guard_name' => 'web',
                'parent' => 'Driver Issue',
                'created_at' => '2026-01-27 17:57:46',
                'updated_at' => '2026-01-27 17:57:46',
            ),
            142 => 
            array (
                'id' => 169,
                'name' => 'Customer Area create',
                'guard_name' => 'web',
                'parent' => 'Customer Area',
                'created_at' => '2026-02-04 18:48:20',
                'updated_at' => '2026-02-04 18:48:20',
            ),
            143 => 
            array (
                'id' => 170,
                'name' => 'Customer Area view',
                'guard_name' => 'web',
                'parent' => 'Customer Area',
                'created_at' => '2026-02-04 18:48:20',
                'updated_at' => '2026-02-04 18:48:20',
            ),
            144 => 
            array (
                'id' => 171,
                'name' => 'Customer Area edit',
                'guard_name' => 'web',
                'parent' => 'Customer Area',
                'created_at' => '2026-02-04 18:48:20',
                'updated_at' => '2026-02-04 18:48:20',
            ),
            145 => 
            array (
                'id' => 172,
                'name' => 'Customer Area destroy',
                'guard_name' => 'web',
                'parent' => 'Customer Area',
                'created_at' => '2026-02-04 18:48:20',
                'updated_at' => '2026-02-04 18:48:20',
            ),
            146 => 
            array (
                'id' => 173,
                'name' => 'Customer Area status',
                'guard_name' => 'web',
                'parent' => 'Customer Area',
                'created_at' => '2026-02-04 18:48:20',
                'updated_at' => '2026-02-04 18:48:20',
            ),
            147 => 
            array (
                'id' => 174,
                'name' => 'Customer Area restore',
                'guard_name' => 'web',
                'parent' => 'Customer Area',
                'created_at' => '2026-02-04 18:48:20',
                'updated_at' => '2026-02-04 18:48:20',
            ),
            148 => 
            array (
                'id' => 175,
                'name' => 'Customer Area delete',
                'guard_name' => 'web',
                'parent' => 'Customer Area',
                'created_at' => '2026-02-04 18:48:20',
                'updated_at' => '2026-02-04 18:48:20',
            ),
            149 => 
            array (
                'id' => 176,
                'name' => 'Customer Area print',
                'guard_name' => 'web',
                'parent' => 'Customer Area',
                'created_at' => '2026-02-04 18:48:20',
                'updated_at' => '2026-02-04 18:48:20',
            ),
            150 => 
            array (
                'id' => 177,
                'name' => 'Customer Area show',
                'guard_name' => 'web',
                'parent' => 'Customer Area',
                'created_at' => '2026-02-04 18:48:21',
                'updated_at' => '2026-02-04 18:48:21',
            ),
            151 => 
            array (
                'id' => 178,
                'name' => 'Customer Area trash',
                'guard_name' => 'web',
                'parent' => 'Customer Area',
                'created_at' => '2026-02-04 18:48:21',
                'updated_at' => '2026-02-04 18:48:21',
            ),
            152 => 
            array (
                'id' => 179,
                'name' => 'Customer create',
                'guard_name' => 'web',
                'parent' => 'Customer',
                'created_at' => '2026-02-04 18:48:46',
                'updated_at' => '2026-02-04 18:48:46',
            ),
            153 => 
            array (
                'id' => 180,
                'name' => 'Customer view',
                'guard_name' => 'web',
                'parent' => 'Customer',
                'created_at' => '2026-02-04 18:48:46',
                'updated_at' => '2026-02-04 18:48:46',
            ),
            154 => 
            array (
                'id' => 181,
                'name' => 'Customer edit',
                'guard_name' => 'web',
                'parent' => 'Customer',
                'created_at' => '2026-02-04 18:48:46',
                'updated_at' => '2026-02-04 18:48:46',
            ),
            155 => 
            array (
                'id' => 182,
                'name' => 'Customer destroy',
                'guard_name' => 'web',
                'parent' => 'Customer',
                'created_at' => '2026-02-04 18:48:46',
                'updated_at' => '2026-02-04 18:48:46',
            ),
            156 => 
            array (
                'id' => 183,
                'name' => 'Customer status',
                'guard_name' => 'web',
                'parent' => 'Customer',
                'created_at' => '2026-02-04 18:48:46',
                'updated_at' => '2026-02-04 18:48:46',
            ),
            157 => 
            array (
                'id' => 184,
                'name' => 'Customer restore',
                'guard_name' => 'web',
                'parent' => 'Customer',
                'created_at' => '2026-02-04 18:48:46',
                'updated_at' => '2026-02-04 18:48:46',
            ),
            158 => 
            array (
                'id' => 185,
                'name' => 'Customer delete',
                'guard_name' => 'web',
                'parent' => 'Customer',
                'created_at' => '2026-02-04 18:48:46',
                'updated_at' => '2026-02-04 18:48:46',
            ),
            159 => 
            array (
                'id' => 186,
                'name' => 'Customer print',
                'guard_name' => 'web',
                'parent' => 'Customer',
                'created_at' => '2026-02-04 18:48:46',
                'updated_at' => '2026-02-04 18:48:46',
            ),
            160 => 
            array (
                'id' => 187,
                'name' => 'Customer show',
                'guard_name' => 'web',
                'parent' => 'Customer',
                'created_at' => '2026-02-04 18:48:46',
                'updated_at' => '2026-02-04 18:48:46',
            ),
            161 => 
            array (
                'id' => 188,
                'name' => 'Customer trash',
                'guard_name' => 'web',
                'parent' => 'Customer',
                'created_at' => '2026-02-04 18:48:47',
                'updated_at' => '2026-02-04 18:48:47',
            ),
            162 => 
            array (
                'id' => 191,
                'name' => 'Sales Ledger create',
                'guard_name' => 'web',
                'parent' => 'Sales Ledger',
                'created_at' => '2026-02-16 18:11:04',
                'updated_at' => '2026-02-16 18:11:04',
            ),
            163 => 
            array (
                'id' => 192,
                'name' => 'Sales Ledger view',
                'guard_name' => 'web',
                'parent' => 'Sales Ledger',
                'created_at' => '2026-02-16 18:11:04',
                'updated_at' => '2026-02-16 18:11:04',
            ),
            164 => 
            array (
                'id' => 193,
                'name' => 'Sales Ledger edit',
                'guard_name' => 'web',
                'parent' => 'Sales Ledger',
                'created_at' => '2026-02-16 18:11:05',
                'updated_at' => '2026-02-16 18:11:05',
            ),
            165 => 
            array (
                'id' => 194,
                'name' => 'Sales Ledger destroy',
                'guard_name' => 'web',
                'parent' => 'Sales Ledger',
                'created_at' => '2026-02-16 18:11:05',
                'updated_at' => '2026-02-16 18:11:05',
            ),
            166 => 
            array (
                'id' => 195,
                'name' => 'Sales Ledger status',
                'guard_name' => 'web',
                'parent' => 'Sales Ledger',
                'created_at' => '2026-02-16 18:11:05',
                'updated_at' => '2026-02-16 18:11:05',
            ),
            167 => 
            array (
                'id' => 196,
                'name' => 'Sales Ledger restore',
                'guard_name' => 'web',
                'parent' => 'Sales Ledger',
                'created_at' => '2026-02-16 18:11:05',
                'updated_at' => '2026-02-16 18:11:05',
            ),
            168 => 
            array (
                'id' => 197,
                'name' => 'Sales Ledger delete',
                'guard_name' => 'web',
                'parent' => 'Sales Ledger',
                'created_at' => '2026-02-16 18:11:05',
                'updated_at' => '2026-02-16 18:11:05',
            ),
            169 => 
            array (
                'id' => 198,
                'name' => 'Sales Ledger print',
                'guard_name' => 'web',
                'parent' => 'Sales Ledger',
                'created_at' => '2026-02-16 18:11:05',
                'updated_at' => '2026-02-16 18:11:05',
            ),
            170 => 
            array (
                'id' => 199,
                'name' => 'Sales Ledger show',
                'guard_name' => 'web',
                'parent' => 'Sales Ledger',
                'created_at' => '2026-02-16 18:11:05',
                'updated_at' => '2026-02-16 18:11:05',
            ),
            171 => 
            array (
                'id' => 200,
                'name' => 'Sales Ledger trash',
                'guard_name' => 'web',
                'parent' => 'Sales Ledger',
                'created_at' => '2026-02-16 18:11:05',
                'updated_at' => '2026-02-16 18:11:05',
            ),
            172 => 
            array (
                'id' => 201,
                'name' => 'Income Expense Title create',
                'guard_name' => 'web',
                'parent' => 'Income Expense Title',
                'created_at' => '2026-02-16 18:15:15',
                'updated_at' => '2026-02-16 18:15:15',
            ),
            173 => 
            array (
                'id' => 202,
                'name' => 'Income Expense Title view',
                'guard_name' => 'web',
                'parent' => 'Income Expense Title',
                'created_at' => '2026-02-16 18:15:16',
                'updated_at' => '2026-02-16 18:15:16',
            ),
            174 => 
            array (
                'id' => 203,
                'name' => 'Income Expense Title edit',
                'guard_name' => 'web',
                'parent' => 'Income Expense Title',
                'created_at' => '2026-02-16 18:15:16',
                'updated_at' => '2026-02-16 18:15:16',
            ),
            175 => 
            array (
                'id' => 204,
                'name' => 'Income Expense Title destroy',
                'guard_name' => 'web',
                'parent' => 'Income Expense Title',
                'created_at' => '2026-02-16 18:15:16',
                'updated_at' => '2026-02-16 18:15:16',
            ),
            176 => 
            array (
                'id' => 205,
                'name' => 'Income Expense Title status',
                'guard_name' => 'web',
                'parent' => 'Income Expense Title',
                'created_at' => '2026-02-16 18:15:16',
                'updated_at' => '2026-02-16 18:15:16',
            ),
            177 => 
            array (
                'id' => 206,
                'name' => 'Income Expense Title restore',
                'guard_name' => 'web',
                'parent' => 'Income Expense Title',
                'created_at' => '2026-02-16 18:15:16',
                'updated_at' => '2026-02-16 18:15:16',
            ),
            178 => 
            array (
                'id' => 207,
                'name' => 'Income Expense Title delete',
                'guard_name' => 'web',
                'parent' => 'Income Expense Title',
                'created_at' => '2026-02-16 18:15:16',
                'updated_at' => '2026-02-16 18:15:16',
            ),
            179 => 
            array (
                'id' => 208,
                'name' => 'Income Expense Title print',
                'guard_name' => 'web',
                'parent' => 'Income Expense Title',
                'created_at' => '2026-02-16 18:15:16',
                'updated_at' => '2026-02-16 18:15:16',
            ),
            180 => 
            array (
                'id' => 209,
                'name' => 'Income Expense Title show',
                'guard_name' => 'web',
                'parent' => 'Income Expense Title',
                'created_at' => '2026-02-16 18:15:16',
                'updated_at' => '2026-02-16 18:15:16',
            ),
            181 => 
            array (
                'id' => 210,
                'name' => 'Income Expense Title trash',
                'guard_name' => 'web',
                'parent' => 'Income Expense Title',
                'created_at' => '2026-02-16 18:15:16',
                'updated_at' => '2026-02-16 18:15:16',
            ),
            182 => 
            array (
                'id' => 211,
                'name' => 'Expense Entry create',
                'guard_name' => 'web',
                'parent' => 'Expense Entry',
                'created_at' => '2026-02-16 18:15:57',
                'updated_at' => '2026-02-16 18:15:57',
            ),
            183 => 
            array (
                'id' => 212,
                'name' => 'Expense Entry view',
                'guard_name' => 'web',
                'parent' => 'Expense Entry',
                'created_at' => '2026-02-16 18:15:57',
                'updated_at' => '2026-02-16 18:15:57',
            ),
            184 => 
            array (
                'id' => 213,
                'name' => 'Expense Entry edit',
                'guard_name' => 'web',
                'parent' => 'Expense Entry',
                'created_at' => '2026-02-16 18:15:57',
                'updated_at' => '2026-02-16 18:15:57',
            ),
            185 => 
            array (
                'id' => 214,
                'name' => 'Expense Entry destroy',
                'guard_name' => 'web',
                'parent' => 'Expense Entry',
                'created_at' => '2026-02-16 18:15:57',
                'updated_at' => '2026-02-16 18:15:57',
            ),
            186 => 
            array (
                'id' => 215,
                'name' => 'Expense Entry status',
                'guard_name' => 'web',
                'parent' => 'Expense Entry',
                'created_at' => '2026-02-16 18:15:57',
                'updated_at' => '2026-02-16 18:15:57',
            ),
            187 => 
            array (
                'id' => 216,
                'name' => 'Expense Entry restore',
                'guard_name' => 'web',
                'parent' => 'Expense Entry',
                'created_at' => '2026-02-16 18:15:57',
                'updated_at' => '2026-02-16 18:15:57',
            ),
            188 => 
            array (
                'id' => 217,
                'name' => 'Expense Entry delete',
                'guard_name' => 'web',
                'parent' => 'Expense Entry',
                'created_at' => '2026-02-16 18:15:57',
                'updated_at' => '2026-02-16 18:15:57',
            ),
            189 => 
            array (
                'id' => 218,
                'name' => 'Expense Entry print',
                'guard_name' => 'web',
                'parent' => 'Expense Entry',
                'created_at' => '2026-02-16 18:15:57',
                'updated_at' => '2026-02-16 18:15:57',
            ),
            190 => 
            array (
                'id' => 219,
                'name' => 'Expense Entry show',
                'guard_name' => 'web',
                'parent' => 'Expense Entry',
                'created_at' => '2026-02-16 18:15:58',
                'updated_at' => '2026-02-16 18:15:58',
            ),
            191 => 
            array (
                'id' => 220,
                'name' => 'Expense Entry trash',
                'guard_name' => 'web',
                'parent' => 'Expense Entry',
                'created_at' => '2026-02-16 18:15:58',
                'updated_at' => '2026-02-16 18:15:58',
            ),
            192 => 
            array (
                'id' => 221,
                'name' => 'Income Entry create',
                'guard_name' => 'web',
                'parent' => 'Income Entry',
                'created_at' => '2026-02-16 18:16:25',
                'updated_at' => '2026-02-16 18:16:25',
            ),
            193 => 
            array (
                'id' => 222,
                'name' => 'Income Entry view',
                'guard_name' => 'web',
                'parent' => 'Income Entry',
                'created_at' => '2026-02-16 18:16:25',
                'updated_at' => '2026-02-16 18:16:25',
            ),
            194 => 
            array (
                'id' => 223,
                'name' => 'Income Entry edit',
                'guard_name' => 'web',
                'parent' => 'Income Entry',
                'created_at' => '2026-02-16 18:16:25',
                'updated_at' => '2026-02-16 18:16:25',
            ),
            195 => 
            array (
                'id' => 224,
                'name' => 'Income Entry destroy',
                'guard_name' => 'web',
                'parent' => 'Income Entry',
                'created_at' => '2026-02-16 18:16:25',
                'updated_at' => '2026-02-16 18:16:25',
            ),
            196 => 
            array (
                'id' => 225,
                'name' => 'Income Entry status',
                'guard_name' => 'web',
                'parent' => 'Income Entry',
                'created_at' => '2026-02-16 18:16:25',
                'updated_at' => '2026-02-16 18:16:25',
            ),
            197 => 
            array (
                'id' => 226,
                'name' => 'Income Entry restore',
                'guard_name' => 'web',
                'parent' => 'Income Entry',
                'created_at' => '2026-02-16 18:16:26',
                'updated_at' => '2026-02-16 18:16:26',
            ),
            198 => 
            array (
                'id' => 227,
                'name' => 'Income Entry delete',
                'guard_name' => 'web',
                'parent' => 'Income Entry',
                'created_at' => '2026-02-16 18:16:26',
                'updated_at' => '2026-02-16 18:16:26',
            ),
            199 => 
            array (
                'id' => 228,
                'name' => 'Income Entry print',
                'guard_name' => 'web',
                'parent' => 'Income Entry',
                'created_at' => '2026-02-16 18:16:26',
                'updated_at' => '2026-02-16 18:16:26',
            ),
            200 => 
            array (
                'id' => 229,
                'name' => 'Income Entry show',
                'guard_name' => 'web',
                'parent' => 'Income Entry',
                'created_at' => '2026-02-16 18:16:26',
                'updated_at' => '2026-02-16 18:16:26',
            ),
            201 => 
            array (
                'id' => 230,
                'name' => 'Income Entry trash',
                'guard_name' => 'web',
                'parent' => 'Income Entry',
                'created_at' => '2026-02-16 18:16:26',
                'updated_at' => '2026-02-16 18:16:26',
            ),
            202 => 
            array (
                'id' => 231,
                'name' => 'Sales Return List view',
                'guard_name' => 'web',
                'parent' => 'Sales Return List',
                'created_at' => '2026-02-20 08:43:14',
                'updated_at' => '2026-02-20 08:43:14',
            ),
            203 => 
            array (
                'id' => 232,
                'name' => 'Sales Return List edit',
                'guard_name' => 'web',
                'parent' => 'Sales Return List',
                'created_at' => '2026-02-20 08:43:14',
                'updated_at' => '2026-02-20 08:43:14',
            ),
            204 => 
            array (
                'id' => 233,
                'name' => 'Sales Return List destroy',
                'guard_name' => 'web',
                'parent' => 'Sales Return List',
                'created_at' => '2026-02-20 08:43:14',
                'updated_at' => '2026-02-20 08:43:14',
            ),
            205 => 
            array (
                'id' => 234,
                'name' => 'Sales Return List status',
                'guard_name' => 'web',
                'parent' => 'Sales Return List',
                'created_at' => '2026-02-20 08:43:14',
                'updated_at' => '2026-02-20 08:43:14',
            ),
            206 => 
            array (
                'id' => 235,
                'name' => 'Sales Return List restore',
                'guard_name' => 'web',
                'parent' => 'Sales Return List',
                'created_at' => '2026-02-20 08:43:14',
                'updated_at' => '2026-02-20 08:43:14',
            ),
            207 => 
            array (
                'id' => 236,
                'name' => 'Sales Return List delete',
                'guard_name' => 'web',
                'parent' => 'Sales Return List',
                'created_at' => '2026-02-20 08:43:14',
                'updated_at' => '2026-02-20 08:43:14',
            ),
            208 => 
            array (
                'id' => 237,
                'name' => 'Sales Return List print',
                'guard_name' => 'web',
                'parent' => 'Sales Return List',
                'created_at' => '2026-02-20 08:43:14',
                'updated_at' => '2026-02-20 08:43:14',
            ),
            209 => 
            array (
                'id' => 238,
                'name' => 'Sales Return List show',
                'guard_name' => 'web',
                'parent' => 'Sales Return List',
                'created_at' => '2026-02-20 08:43:14',
                'updated_at' => '2026-02-20 08:43:14',
            ),
            210 => 
            array (
                'id' => 239,
                'name' => 'Sales Return List trash',
                'guard_name' => 'web',
                'parent' => 'Sales Return List',
                'created_at' => '2026-02-20 08:43:14',
                'updated_at' => '2026-02-20 08:43:14',
            ),
            211 => 
            array (
                'id' => 240,
                'name' => 'Website Settings create',
                'guard_name' => 'web',
                'parent' => 'Website Settings',
                'created_at' => '2026-02-20 08:47:10',
                'updated_at' => '2026-02-20 08:47:10',
            ),
            212 => 
            array (
                'id' => 241,
                'name' => 'Website Settings view',
                'guard_name' => 'web',
                'parent' => 'Website Settings',
                'created_at' => '2026-02-20 08:47:10',
                'updated_at' => '2026-02-20 08:47:10',
            ),
            213 => 
            array (
                'id' => 242,
                'name' => 'Website Settings edit',
                'guard_name' => 'web',
                'parent' => 'Website Settings',
                'created_at' => '2026-02-20 08:47:10',
                'updated_at' => '2026-02-20 08:47:10',
            ),
            214 => 
            array (
                'id' => 243,
                'name' => 'Website Settings destroy',
                'guard_name' => 'web',
                'parent' => 'Website Settings',
                'created_at' => '2026-02-20 08:47:10',
                'updated_at' => '2026-02-20 08:47:10',
            ),
            215 => 
            array (
                'id' => 244,
                'name' => 'Website Settings status',
                'guard_name' => 'web',
                'parent' => 'Website Settings',
                'created_at' => '2026-02-20 08:47:10',
                'updated_at' => '2026-02-20 08:47:10',
            ),
            216 => 
            array (
                'id' => 245,
                'name' => 'Website Settings restore',
                'guard_name' => 'web',
                'parent' => 'Website Settings',
                'created_at' => '2026-02-20 08:47:10',
                'updated_at' => '2026-02-20 08:47:10',
            ),
            217 => 
            array (
                'id' => 246,
                'name' => 'Website Settings delete',
                'guard_name' => 'web',
                'parent' => 'Website Settings',
                'created_at' => '2026-02-20 08:47:10',
                'updated_at' => '2026-02-20 08:47:10',
            ),
            218 => 
            array (
                'id' => 247,
                'name' => 'Website Settings print',
                'guard_name' => 'web',
                'parent' => 'Website Settings',
                'created_at' => '2026-02-20 08:47:10',
                'updated_at' => '2026-02-20 08:47:10',
            ),
            219 => 
            array (
                'id' => 248,
                'name' => 'Website Settings show',
                'guard_name' => 'web',
                'parent' => 'Website Settings',
                'created_at' => '2026-02-20 08:47:10',
                'updated_at' => '2026-02-20 08:47:10',
            ),
            220 => 
            array (
                'id' => 249,
                'name' => 'Website Settings trash',
                'guard_name' => 'web',
                'parent' => 'Website Settings',
                'created_at' => '2026-02-20 08:47:11',
                'updated_at' => '2026-02-20 08:47:11',
            ),
            221 => 
            array (
                'id' => 250,
                'name' => 'Purchase Return create',
                'guard_name' => 'web',
                'parent' => 'Purchase Return',
                'created_at' => '2026-02-20 09:00:56',
                'updated_at' => '2026-02-20 09:00:56',
            ),
            222 => 
            array (
                'id' => 251,
                'name' => 'Purchase Return view',
                'guard_name' => 'web',
                'parent' => 'Purchase Return',
                'created_at' => '2026-02-20 09:00:56',
                'updated_at' => '2026-02-20 09:00:56',
            ),
            223 => 
            array (
                'id' => 252,
                'name' => 'Purchase Return edit',
                'guard_name' => 'web',
                'parent' => 'Purchase Return',
                'created_at' => '2026-02-20 09:00:56',
                'updated_at' => '2026-02-20 09:00:56',
            ),
            224 => 
            array (
                'id' => 253,
                'name' => 'Purchase Return destroy',
                'guard_name' => 'web',
                'parent' => 'Purchase Return',
                'created_at' => '2026-02-20 09:00:56',
                'updated_at' => '2026-02-20 09:00:56',
            ),
            225 => 
            array (
                'id' => 254,
                'name' => 'Purchase Return status',
                'guard_name' => 'web',
                'parent' => 'Purchase Return',
                'created_at' => '2026-02-20 09:00:56',
                'updated_at' => '2026-02-20 09:00:56',
            ),
            226 => 
            array (
                'id' => 255,
                'name' => 'Purchase Return restore',
                'guard_name' => 'web',
                'parent' => 'Purchase Return',
                'created_at' => '2026-02-20 09:00:56',
                'updated_at' => '2026-02-20 09:00:56',
            ),
            227 => 
            array (
                'id' => 256,
                'name' => 'Purchase Return delete',
                'guard_name' => 'web',
                'parent' => 'Purchase Return',
                'created_at' => '2026-02-20 09:00:57',
                'updated_at' => '2026-02-20 09:00:57',
            ),
            228 => 
            array (
                'id' => 257,
                'name' => 'Purchase Return print',
                'guard_name' => 'web',
                'parent' => 'Purchase Return',
                'created_at' => '2026-02-20 09:00:57',
                'updated_at' => '2026-02-20 09:00:57',
            ),
            229 => 
            array (
                'id' => 258,
                'name' => 'Purchase Return show',
                'guard_name' => 'web',
                'parent' => 'Purchase Return',
                'created_at' => '2026-02-20 09:00:57',
                'updated_at' => '2026-02-20 09:00:57',
            ),
            230 => 
            array (
                'id' => 259,
                'name' => 'Purchase Return trash',
                'guard_name' => 'web',
                'parent' => 'Purchase Return',
                'created_at' => '2026-02-20 09:00:57',
                'updated_at' => '2026-02-20 09:00:57',
            ),
            231 => 
            array (
                'id' => 260,
                'name' => 'Supplier Payment create',
                'guard_name' => 'web',
                'parent' => 'Supplier Payment',
                'created_at' => '2026-02-20 13:25:58',
                'updated_at' => '2026-02-20 13:25:58',
            ),
            232 => 
            array (
                'id' => 261,
                'name' => 'Supplier Payment view',
                'guard_name' => 'web',
                'parent' => 'Supplier Payment',
                'created_at' => '2026-02-20 13:25:59',
                'updated_at' => '2026-02-20 13:25:59',
            ),
            233 => 
            array (
                'id' => 262,
                'name' => 'Supplier Payment edit',
                'guard_name' => 'web',
                'parent' => 'Supplier Payment',
                'created_at' => '2026-02-20 13:25:59',
                'updated_at' => '2026-02-20 13:25:59',
            ),
            234 => 
            array (
                'id' => 263,
                'name' => 'Supplier Payment destroy',
                'guard_name' => 'web',
                'parent' => 'Supplier Payment',
                'created_at' => '2026-02-20 13:25:59',
                'updated_at' => '2026-02-20 13:25:59',
            ),
            235 => 
            array (
                'id' => 264,
                'name' => 'Supplier Payment status',
                'guard_name' => 'web',
                'parent' => 'Supplier Payment',
                'created_at' => '2026-02-20 13:25:59',
                'updated_at' => '2026-02-20 13:25:59',
            ),
            236 => 
            array (
                'id' => 265,
                'name' => 'Supplier Payment restore',
                'guard_name' => 'web',
                'parent' => 'Supplier Payment',
                'created_at' => '2026-02-20 13:25:59',
                'updated_at' => '2026-02-20 13:25:59',
            ),
            237 => 
            array (
                'id' => 266,
                'name' => 'Supplier Payment delete',
                'guard_name' => 'web',
                'parent' => 'Supplier Payment',
                'created_at' => '2026-02-20 13:25:59',
                'updated_at' => '2026-02-20 13:25:59',
            ),
            238 => 
            array (
                'id' => 267,
                'name' => 'Supplier Payment print',
                'guard_name' => 'web',
                'parent' => 'Supplier Payment',
                'created_at' => '2026-02-20 13:25:59',
                'updated_at' => '2026-02-20 13:25:59',
            ),
            239 => 
            array (
                'id' => 268,
                'name' => 'Supplier Payment show',
                'guard_name' => 'web',
                'parent' => 'Supplier Payment',
                'created_at' => '2026-02-20 13:25:59',
                'updated_at' => '2026-02-20 13:25:59',
            ),
            240 => 
            array (
                'id' => 269,
                'name' => 'Supplier Payment trash',
                'guard_name' => 'web',
                'parent' => 'Supplier Payment',
                'created_at' => '2026-02-20 13:25:59',
                'updated_at' => '2026-02-20 13:25:59',
            ),
        ));
        
        
    }
}