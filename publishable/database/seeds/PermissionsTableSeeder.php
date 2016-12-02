<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        // visit_admin
        Permission::firstOrCreate([
            'key' => 'visit_admin',
            'table_name' => 'admin',
        ]);

        // visit_database
        Permission::firstOrCreate([
            'key' => 'visit_database',
            'table_name' => 'admin',
        ]);

        // visit_media
        Permission::firstOrCreate([
            'key' => 'visit_media',
            'table_name' => 'admin',
        ]);

        // visit_settings
        Permission::firstOrCreate([
            'key' => 'visit_settings',
            'table_name' => 'admin',
        ]);

        // menus
        Permission::generateFor('menus');

        // pages
        Permission::generateFor('pages');

        // roles
        Permission::generateFor('roles');

        // users
        Permission::generateFor('users');

        // posts
        Permission::generateFor('posts');

        // categories
        Permission::generateFor('categories');
    }
}
