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
        ));
        
        
    }
}