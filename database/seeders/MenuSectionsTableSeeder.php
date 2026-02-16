<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MenuSectionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('menu_sections')->delete();
        
        \DB::table('menu_sections')->insert(array (
            0 => 
            array (
                'id' => 2,
                'sl' => 1,
                'name' => 'Dashboard',
                'status' => 1,
                'created_at' => '2025-12-03 19:17:19',
                'updated_at' => '2025-12-03 19:17:19',
            ),
            1 => 
            array (
                'id' => 3,
                'sl' => 2,
                'name' => 'Developer Sectino',
                'status' => 1,
                'created_at' => '2025-12-08 16:44:09',
                'updated_at' => '2025-12-08 16:44:09',
            ),
            2 => 
            array (
                'id' => 4,
                'sl' => 3,
                'name' => 'Authorization',
                'status' => 1,
                'created_at' => '2025-12-08 17:10:11',
                'updated_at' => '2025-12-08 17:10:11',
            ),
            3 => 
            array (
                'id' => 5,
                'sl' => 4,
                'name' => 'Website Content',
                'status' => 1,
                'created_at' => '2025-12-08 18:13:02',
                'updated_at' => '2025-12-08 18:13:02',
            ),
            4 => 
            array (
                'id' => 6,
                'sl' => 5,
                'name' => 'Product Components',
                'status' => 1,
                'created_at' => '2025-12-14 18:10:05',
                'updated_at' => '2025-12-14 18:10:05',
            ),
            5 => 
            array (
                'id' => 7,
                'sl' => 6,
                'name' => 'Supplier & Customer',
                'status' => 1,
                'created_at' => '2025-12-17 16:56:46',
                'updated_at' => '2025-12-17 16:56:46',
            ),
            6 => 
            array (
                'id' => 8,
                'sl' => 7,
                'name' => 'Purchase',
                'status' => 1,
                'created_at' => '2025-12-19 14:20:32',
                'updated_at' => '2025-12-19 14:20:32',
            ),
            7 => 
            array (
                'id' => 9,
                'sl' => 8,
                'name' => 'Stock',
                'status' => 1,
                'created_at' => '2026-01-11 17:24:26',
                'updated_at' => '2026-01-11 17:24:26',
            ),
            8 => 
            array (
                'id' => 10,
                'sl' => 9,
                'name' => 'Driver Section',
                'status' => 1,
                'created_at' => '2026-01-25 16:50:49',
                'updated_at' => '2026-01-25 16:50:49',
            ),
            9 => 
            array (
                'id' => 11,
                'sl' => 10,
                'name' => 'Customer',
                'status' => 1,
                'created_at' => '2026-02-04 18:47:17',
                'updated_at' => '2026-02-04 18:47:17',
            ),
            10 => 
            array (
                'id' => 12,
                'sl' => 11,
                'name' => 'Settings',
                'status' => 1,
                'created_at' => '2026-02-16 18:03:02',
                'updated_at' => '2026-02-16 18:03:02',
            ),
            11 => 
            array (
                'id' => 13,
                'sl' => 12,
                'name' => 'Sales',
                'status' => 1,
                'created_at' => '2026-02-16 18:09:12',
                'updated_at' => '2026-02-16 18:09:12',
            ),
            12 => 
            array (
                'id' => 14,
                'sl' => 13,
                'name' => 'Income Expense',
                'status' => 1,
                'created_at' => '2026-02-16 18:13:48',
                'updated_at' => '2026-02-16 18:13:48',
            ),
        ));
        
        
    }
}