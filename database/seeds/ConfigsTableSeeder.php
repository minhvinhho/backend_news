<?php

use Illuminate\Database\Seeder;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Config::insert([
            ['name' => 'site_name', 'value' => 'BAP NEWS'],
            ['name' => 'site_title', 'value' => 'BAP NEWS'],
            ['name' => 'copyright_owner', 'value' => 'bap-jp'],
            ['name' => 'admin_email', 'value' => 'hominh4078@gmail.com'],
        ]);
    }
}
