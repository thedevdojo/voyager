<?php

use Illuminate\Database\Seeder;

class MenuItemsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {

        if (file_exists(base_path('routes/vendor/voyager.php'))) {
            require base_path('routes/vendor/voyager.php');

            \DB::table('menu_items')->delete();

            \DB::table('menu_items')->insert([
                0 => [
                    'id' => 1,
                    'menu_id' => 2,
                    'title' => 'Dashboard',
                    'url' => route('voyager.dashboard', [], false),
                    'target' => '_self',
                    'icon_class' => 'voyager-boat',
                    'color' => null,
                    'parent_id' => null,
                    'order' => 1,
                    'created_at' => '2016-05-31 22:17:38',
                    'updated_at' => '2016-06-01 20:24:01',
                ],
                1 => [
                    'id' => 2,
                    'menu_id' => 2,
                    'title' => 'Media',
                    'url' => route('voyager.media', [], false),
                    'target' => '_self',
                    'icon_class' => 'voyager-images',
                    'color' => null,
                    'parent_id' => null,
                    'order' => 5,
                    'created_at' => '2016-05-31 22:18:08',
                    'updated_at' => '2016-06-01 20:24:01',
                ],
                2 => [
                    'id' => 3,
                    'menu_id' => 2,
                    'title' => 'Posts',
                    'url' => route('posts.index', [], false),
                    'target' => '_self',
                    'icon_class' => 'voyager-news',
                    'color' => null,
                    'parent_id' => null,
                    'order' => 6,
                    'created_at' => '2016-05-31 22:18:37',
                    'updated_at' => '2016-06-01 16:51:43',
                ],
                3 => [
                    'id' => 4,
                    'menu_id' => 2,
                    'title' => 'Users',
                    'url' => route('users.index', [], false),
                    'target' => '_self',
                    'icon_class' => 'voyager-person',
                    'color' => null,
                    'parent_id' => null,
                    'order' => 3,
                    'created_at' => '2016-05-31 22:19:16',
                    'updated_at' => '2016-05-31 22:20:07',
                ],
                4 => [
                    'id' => 5,
                    'menu_id' => 2,
                    'title' => 'Categories',
                    'url' => route('categories.index', [], false),
                    'target' => '_self',
                    'icon_class' => 'voyager-categories',
                    'color' => null,
                    'parent_id' => null,
                    'order' => 8,
                    'created_at' => '2016-05-31 22:19:38',
                    'updated_at' => '2016-06-01 20:07:46',
                ],
                5 => [
                    'id' => 6,
                    'menu_id' => 2,
                    'title' => 'Pages',
                    'url' => route('pages.index', [], false),
                    'target' => '_self',
                    'icon_class' => 'voyager-file-text',
                    'color' => null,
                    'parent_id' => null,
                    'order' => 7,
                    'created_at' => '2016-05-31 22:20:03',
                    'updated_at' => '2016-06-01 16:51:43',
                ],
                6 => [
                    'id' => 7,
                    'menu_id' => 2,
                    'title' => 'Roles',
                    'url' => route('roles.index', [], false),
                    'target' => '_self',
                    'icon_class' => 'voyager-lock',
                    'color' => null,
                    'parent_id' => null,
                    'order' => 2,
                    'created_at' => '2016-10-21 19:14:25',
                    'updated_at' => '2016-10-24 00:44:07',
                ],
            ]);
        }
    }
}
