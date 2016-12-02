<?php

use Illuminate\Database\Seeder;

class DataTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('data_types')->delete();

        \DB::table('data_types')->insert([
            0 => [
                'id'                    => 1,
                'name'                  => 'posts',
                'slug'                  => 'posts',
                'display_name_singular' => 'Post',
                'display_name_plural'   => 'Posts',
                'icon'                  => 'voyager-news',
                'model_name'            => 'TCG\\Voyager\\Models\\Post',
                'generate_permissions'  => 1,
                'description'           => '',
                'created_at'            => '2016-01-27 19:45:51',
                'updated_at'            => '2016-01-28 03:45:51',
            ],
            1 => [
                'id'                    => 3,
                'name'                  => 'pages',
                'slug'                  => 'pages',
                'display_name_singular' => 'Page',
                'display_name_plural'   => 'Pages',
                'icon'                  => 'voyager-file-text',
                'model_name'            => 'TCG\\Voyager\\Models\\Page',
                'generate_permissions'  => 1,
                'description'           => '',
                'created_at'            => '2016-02-02 02:37:02',
                'updated_at'            => '2016-02-02 02:37:02',
            ],
            2 => [
                'id'                    => 4,
                'name'                  => 'users',
                'slug'                  => 'users',
                'display_name_singular' => 'User',
                'display_name_plural'   => 'Users',
                'icon'                  => 'voyager-person',
                'model_name'            => 'TCG\\Voyager\\Models\\User',
                'generate_permissions'  => 1,
                'description'           => '',
                'created_at'            => '2016-01-27 19:43:51',
                'updated_at'            => '2016-02-03 02:07:20',
            ],
            3 => [
                'id'                    => 5,
                'name'                  => 'categories',
                'slug'                  => 'categories',
                'display_name_singular' => 'Category',
                'display_name_plural'   => 'Categories',
                'icon'                  => 'voyager-categories',
                'model_name'            => 'TCG\\Voyager\\Models\\Category',
                'generate_permissions'  => 1,
                'description'           => '',
                'created_at'            => null,
                'updated_at'            => '2016-06-29 00:18:42',
            ],
            4 => [
                'id'                    => 6,
                'name'                  => 'menus',
                'slug'                  => 'menus',
                'display_name_singular' => 'Menu',
                'display_name_plural'   => 'Menus',
                'icon'                  => 'voyager-list',
                'model_name'            => 'TCG\\Voyager\\Models\\Menu',
                'generate_permissions'  => 1,
                'description'           => '',
                'created_at'            => null,
                'updated_at'            => '2016-06-29 00:09:35',
            ],
            5 => [
                'id'                    => 8,
                'name'                  => 'roles',
                'slug'                  => 'roles',
                'display_name_singular' => 'Role',
                'display_name_plural'   => 'Roles',
                'icon'                  => 'voyager-lock',
                'model_name'            => 'TCG\\Voyager\\Models\\Role',
                'generate_permissions'  => 1,
                'description'           => '',
                'created_at'            => '2016-10-21 22:09:45',
                'updated_at'            => '2016-10-21 22:09:45',
            ],
        ]);
    }
}
