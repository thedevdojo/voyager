<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('settings')->delete();

        \DB::table('settings')->insert([
            0 => [
                'id'           => 1,
                'key'          => 'title',
                'display_name' => 'Site Title',
                'value'        => 'Site Title',
                'details'      => '',
                'type'         => 'text',
                'order'        => 1,
            ],
            1 => [
                'id'           => 2,
                'key'          => 'description',
                'display_name' => 'Site Description',
                'value'        => 'Site Description',
                'details'      => '',
                'type'         => 'text',
                'order'        => 2,
            ],
            2 => [
                'id'           => 3,
                'key'          => 'logo',
                'display_name' => 'Site Logo',
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 3,
            ],
            3 => [
                'id'           => 7,
                'key'          => 'admin_bg_image',
                'display_name' => 'Admin Background Image',
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 6,
            ],
            4 => [
                'id'           => 8,
                'key'          => 'admin_title',
                'display_name' => 'Admin Title',
                'value'        => 'Voyager',
                'details'      => '',
                'type'         => 'text',
                'order'        => 4,
            ],
            5 => [
                'id'           => 9,
                'key'          => 'admin_description',
                'display_name' => 'Admin Description',
                'value'        => 'Welcome to Voyager. The Missing Admin for Laravel',
                'details'      => '',
                'type'         => 'text',
                'order'        => 5,
            ],
            6 => [
                'id'           => 10,
                'key'          => 'google_analytics_client_id',
                'display_name' => 'Google Analytics Client ID',
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 6,
            ],
        ]);
    }
}
