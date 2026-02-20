<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WebsiteSettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('website_settings')->delete();
        
        \DB::table('website_settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'logo' => '0',
                'favicon' => '0',
                'title' => 'MEHEDI ENTERPRISE',
                'phone' => '+880 133-9700077',
                'address' => 'Trank Road, Feni – 3900',
                'created_at' => NULL,
                'updated_at' => '2026-02-20 17:18:01',
                'name' => 'Sakhawat Hossen',
                'email' => 'sakhawathossen5895@gmail.com',
                'designation' => 'Chief Executive Officer',
                'slogan' => 'Trust Begins With Quality',
            ),
        ));
        
        
    }
}