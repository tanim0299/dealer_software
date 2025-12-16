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
            7 => 
            array (
                'id' => 11,
                'sl' => 8,
                'menu_section_id' => 5,
                'parent_id' => NULL,
                'name' => 'Website Content',
                'system_name' => NULL,
                'route' => NULL,
                'slug' => NULL,
                'icon' => 'fa fa-gear',
                'type' => 1,
                'status' => 1,
                'created_at' => '2025-12-08 18:14:59',
                'updated_at' => '2025-12-08 18:14:59',
            ),
            8 => 
            array (
                'id' => 12,
                'sl' => 9,
                'menu_section_id' => 5,
                'parent_id' => 11,
                'name' => 'Website Settings',
                'system_name' => 'Website Settings',
                'route' => 'website_settings',
                'slug' => 'index',
                'icon' => NULL,
                'type' => 2,
                'status' => 1,
                'created_at' => '2025-12-08 18:16:06',
                'updated_at' => '2025-12-08 18:16:06',
            ),
            9 => 
            array (
                'id' => 13,
                'sl' => 10,
                'menu_section_id' => 6,
                'parent_id' => NULL,
                'name' => 'Product Components',
                'system_name' => NULL,
                'route' => NULL,
                'slug' => NULL,
                'icon' => 'fa fa-box',
                'type' => 1,
                'status' => 1,
                'created_at' => '2025-12-14 18:12:24',
                'updated_at' => '2025-12-14 18:12:24',
            ),
            10 => 
            array (
                'id' => 15,
                'sl' => 12,
                'menu_section_id' => 6,
                'parent_id' => 13,
                'name' => 'Item',
                'system_name' => 'Item',
                'route' => 'item',
                'slug' => 'index',
                'icon' => NULL,
                'type' => 2,
                'status' => 1,
                'created_at' => '2025-12-14 18:17:58',
                'updated_at' => '2025-12-14 18:25:51',
            ),
            11 => 
            array (
                'id' => 16,
                'sl' => 13,
                'menu_section_id' => 6,
                'parent_id' => 13,
                'name' => 'Category',
                'system_name' => 'Category',
                'route' => 'category',
                'slug' => 'index',
                'icon' => NULL,
                'type' => 2,
                'status' => 1,
                'created_at' => '2025-12-14 18:27:51',
                'updated_at' => '2025-12-14 18:27:51',
            ),
            12 => 
            array (
                'id' => 17,
                'sl' => 14,
                'menu_section_id' => 6,
                'parent_id' => 13,
                'name' => 'Brand',
                'system_name' => 'Brand',
                'route' => 'brand',
                'slug' => 'index',
                'icon' => NULL,
                'type' => 2,
                'status' => 1,
                'created_at' => '2025-12-14 18:29:13',
                'updated_at' => '2025-12-14 18:29:13',
            ),
            13 => 
            array (
                'id' => 18,
                'sl' => 15,
                'menu_section_id' => 6,
                'parent_id' => 13,
                'name' => 'Unit',
                'system_name' => 'Unit',
                'route' => 'unit',
                'slug' => 'index',
                'icon' => NULL,
                'type' => 2,
                'status' => 1,
                'created_at' => '2025-12-16 06:26:02',
                'updated_at' => '2025-12-16 06:26:02',
            ),
            14 => 
            array (
                'id' => 19,
                'sl' => 16,
                'menu_section_id' => 6,
                'parent_id' => 13,
                'name' => 'Sub Unit',
                'system_name' => 'Sub Unit',
                'route' => 'sub_unit',
                'slug' => 'index',
                'icon' => NULL,
                'type' => 2,
                'status' => 1,
                'created_at' => '2025-12-16 06:26:37',
                'updated_at' => '2025-12-16 06:26:37',
            ),
        ));
        
        
    }
}