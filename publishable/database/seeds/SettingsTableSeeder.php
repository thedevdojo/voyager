<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        // Site Title
        $setting = Setting::firstOrNew([
            'key' => 'title',
        ]);
        if (!$setting->exists) {
            $setting->fill([
                'type'         => 'text',
                'display_name' => 'Site Title',
                'value'        => 'Site Title',
                'details'      => '',
                'order'        => 1,
            ])->save();
        }

        // Site Description
        $setting = Setting::firstOrNew([
            'key' => 'description',
        ]);
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Site Description',
                'value'        => 'Site Description',
                'details'      => '',
                'type'         => 'text',
                'order'        => 2,
            ])->save();
        }

        // Site Logo
        $setting = Setting::firstOrNew([
            'key' => 'logo',
        ]);
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Site Logo',
                'value'        => 'Site Logo',
                'details'      => '',
                'type'         => 'image',
                'order'        => 3,
            ])->save();
        }

        // Admin Title
        $setting = Setting::firstOrNew([
            'key' => 'admin_title',
        ]);
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Admin Title',
                'value'        => 'Voyager',
                'details'      => '',
                'type'         => 'text',
                'order'        => 4,
            ])->save();
        }

        // Admin Description
        $setting = Setting::firstOrNew([
            'key' => 'admin_description',
        ]);
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Admin Description',
                'value'        => 'Welcome to Voyager. The Missing Admin for Laravel',
                'details'      => '',
                'type'         => 'text',
                'order'        => 5,
            ])->save();
        }

        // Admin Background Image
        $setting = Setting::firstOrNew([
            'key' => 'admin_bg_image',
        ]);
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Admin Background Image',
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 6,
            ])->save();
        }

        // Google Analytics Client ID
        $setting = Setting::firstOrNew([
            'key' => 'google_analytics_client_id',
        ]);
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Google Analytics Client ID',
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 7,
            ])->save();
        }
    }
}
