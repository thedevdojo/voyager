<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $keys = [
            'browse_admin',

            'browse_bread',
            'read_bread',
            'add_bread',
            'edit_bread',
            'delete_bread',

            'browse_database',
            'clean_database',
            'read_database',
            'add_database',
            'edit_database',
            'delete_database',

            'browse_media',
            'read_media',
            'upload_media',
            'folder_media',
            'delete_media',
            'move_media',
            'rename_media',
            'crop_media',

            'browse_compass',
        ];

        foreach ($keys as $key) {
            Permission::firstOrCreate([
                'key'        => $key,
                'table_name' => null,
            ]);
        }

        Permission::generateFor('menus');

        Permission::generateFor('roles');

        Permission::generateFor('users');

        Permission::generateFor('settings');
    }
}
