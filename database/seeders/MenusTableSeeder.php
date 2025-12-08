<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('menus')->delete();
        
        \DB::table('menus')->insert(array (
            0 => 
            array (
                'id' => 4,
                'sl' => 1,
                'menu_section_id' => 2,
                'parent_id' => NULL,
                'name' => 'Dashboard',
                'system_name' => 'Dashboard',
                'route' => 'dashboard',
                'slug' => 'index',
                'icon' => 'fa fa-home',
                'type' => 3,
                'status' => 1,
                'created_at' => '2025-12-08 16:45:13',
                'updated_at' => '2025-12-08 16:45:13',
            ),
            1 => 
            array (
                'id' => 5,
                'sl' => 2,
                'menu_section_id' => 3,
                'parent_id' => NULL,
                'name' => 'Developer Menu',
                'system_name' => NULL,
                'route' => NULL,
                'slug' => NULL,
                'icon' => 'fa fa-bars',
                'type' => 1,
                'status' => 1,
                'created_at' => '2025-12-08 16:45:33',
                'updated_at' => '2025-12-08 16:45:33',
            ),
            2 => 
            array (
                'id' => 6,
                'sl' => 3,
                'menu_section_id' => 3,
                'parent_id' => 5,
                'name' => 'Menu Section',
                'system_name' => 'Menu Section',
                'route' => 'menu_section',
                'slug' => 'index',
                'icon' => NULL,
                'type' => 2,
                'status' => 1,
                'created_at' => '2025-12-08 16:46:52',
                'updated_at' => '2025-12-08 16:46:52',
            ),
            3 => 
            array (
                'id' => 7,
                'sl' => 4,
                'menu_section_id' => 3,
                'parent_id' => 5,
                'name' => 'Menu',
                'system_name' => 'Menu',
                'route' => 'menu',
                'slug' => 'index',
                'icon' => NULL,
                'type' => 2,
                'status' => 1,
                'created_at' => '2025-12-08 16:47:12',
                'updated_at' => '2025-12-08 16:47:12',
            ),
            4 => 
            array (
                'id' => 8,
                'sl' => 5,
                'menu_section_id' => 4,
                'parent_id' => NULL,
                'name' => 'User Management',
                'system_name' => NULL,
                'route' => NULL,
                'slug' => NULL,
                'icon' => 'fa fa-users',
                'type' => 1,
                'status' => 1,
                'created_at' => '2025-12-08 17:10:36',
                'updated_at' => '2025-12-08 17:10:36',
            ),
            5 => 
            array (
                'id' => 9,
                'sl' => 6,
                'menu_section_id' => 4,
                'parent_id' => 8,
                'name' => 'Role',
                'system_name' => 'Role',
                'route' => 'role',
                'slug' => 'index',
                'icon' => NULL,
                'type' => 2,
                'status' => 1,
                'created_at' => '2025-12-08 17:11:03',
                'updated_at' => '2025-12-08 17:11:03',
            ),
            6 => 
            array (
                'id' => 10,
                'sl' => 7,
                'menu_section_id' => 4,
                'parent_id' => 8,
                'name' => 'User',
                'system_name' => 'User',
                'route' => 'user',
                'slug' => 'index',
                'icon' => NULL,
                'type' => 2,
                'status' => 1,
                'created_at' => '2025-12-08 17:11:29',
                'updated_at' => '2025-12-08 17:11:29',
            ),
        ));
        
        
    }
}