<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('permissions')->delete();

        \DB::table('permissions')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'key' => 'visit_admin',
                    'table_name' => 'admin',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            1 =>
                array (
                    'id' => 2,
                    'key' => 'browse_menus',
                    'table_name' => 'menus',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            2 =>
                array (
                    'id' => 3,
                    'key' => 'read_menus',
                    'table_name' => 'menus',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            3 =>
                array (
                    'id' => 4,
                    'key' => 'edit_menus',
                    'table_name' => 'menus',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            4 =>
                array (
                    'id' => 5,
                    'key' => 'add_menus',
                    'table_name' => 'menus',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            5 =>
                array (
                    'id' => 6,
                    'key' => 'delete_menus',
                    'table_name' => 'menus',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            6 =>
                array (
                    'id' => 7,
                    'key' => 'browse_pages',
                    'table_name' => 'pages',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            7 =>
                array (
                    'id' => 8,
                    'key' => 'read_pages',
                    'table_name' => 'pages',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            8 =>
                array (
                    'id' => 9,
                    'key' => 'edit_pages',
                    'table_name' => 'pages',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            9 =>
                array (
                    'id' => 10,
                    'key' => 'add_pages',
                    'table_name' => 'pages',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            10 =>
                array (
                    'id' => 11,
                    'key' => 'delete_pages',
                    'table_name' => 'pages',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            11 =>
                array (
                    'id' => 12,
                    'key' => 'browse_roles',
                    'table_name' => 'roles',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            12 =>
                array (
                    'id' => 13,
                    'key' => 'read_roles',
                    'table_name' => 'roles',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            13 =>
                array (
                    'id' => 14,
                    'key' => 'edit_roles',
                    'table_name' => 'roles',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            14 =>
                array (
                    'id' => 15,
                    'key' => 'add_roles',
                    'table_name' => 'roles',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            15 =>
                array (
                    'id' => 16,
                    'key' => 'delete_roles',
                    'table_name' => 'roles',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            16 =>
                array (
                    'id' => 17,
                    'key' => 'browse_users',
                    'table_name' => 'users',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            17 =>
                array (
                    'id' => 18,
                    'key' => 'read_users',
                    'table_name' => 'users',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            18 =>
                array (
                    'id' => 19,
                    'key' => 'edit_users',
                    'table_name' => 'users',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            19 =>
                array (
                    'id' => 20,
                    'key' => 'add_users',
                    'table_name' => 'users',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            20 =>
                array (
                    'id' => 21,
                    'key' => 'delete_users',
                    'table_name' => 'users',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            21 =>
                array (
                    'id' => 22,
                    'key' => 'browse_posts',
                    'table_name' => 'posts',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            22 =>
                array (
                    'id' => 23,
                    'key' => 'read_posts',
                    'table_name' => 'posts',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            23 =>
                array (
                    'id' => 24,
                    'key' => 'edit_posts',
                    'table_name' => 'posts',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            24 =>
                array (
                    'id' => 25,
                    'key' => 'add_posts',
                    'table_name' => 'posts',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            25 =>
                array (
                    'id' => 26,
                    'key' => 'delete_posts',
                    'table_name' => 'posts',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            26 =>
                array (
                    'id' => 27,
                    'key' => 'browse_categories',
                    'table_name' => 'categories',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            27 =>
                array (
                    'id' => 28,
                    'key' => 'read_categories',
                    'table_name' => 'categories',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            28 =>
                array (
                    'id' => 29,
                    'key' => 'edit_categories',
                    'table_name' => 'categories',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            29 =>
                array (
                    'id' => 30,
                    'key' => 'add_categories',
                    'table_name' => 'categories',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
            30 =>
                array (
                    'id' => 31,
                    'key' => 'delete_categories',
                    'table_name' => 'categories',
                    'created_at' => '2016-10-21 22:31:20',
                    'updated_at' => '2016-10-21 22:31:20',
                ),
        ));


    }
}
