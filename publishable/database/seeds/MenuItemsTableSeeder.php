<?php

use Illuminate\Database\Seeder;

class MenuItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        $prefix = config('voyager.routes.prefix', 'admin');

        \DB::table('menu_items')->delete();
        
        \DB::table('menu_items')->insert(array (
            0 => 
            array (
                'id' => 1,
                'menu_id' => 2,
                'title' => 'Dashboard',
                'url' => "/{$prefix}",
                'target' => '_self',
                'icon_class' => 'voyager-boat',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 1,
                'created_at' => '2016-05-31 22:17:38',
                'updated_at' => '2016-06-01 20:24:01',
            ),
            1 => 
            array (
                'id' => 2,
                'menu_id' => 2,
                'title' => 'Media',
                'url' => "/{$prefix}/media",
                'target' => '_self',
                'icon_class' => 'voyager-images',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 5,
                'created_at' => '2016-05-31 22:18:08',
                'updated_at' => '2016-06-01 20:24:01',
            ),
            2 => 
            array (
                'id' => 3,
                'menu_id' => 2,
                'title' => 'Posts',
                'url' => "/{$prefix}/posts",
                'target' => '_self',
                'icon_class' => 'voyager-news',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 6,
                'created_at' => '2016-05-31 22:18:37',
                'updated_at' => '2016-06-01 16:51:43',
            ),
            3 => 
            array (
                'id' => 4,
                'menu_id' => 2,
                'title' => 'Users',
                'url' => "/{$prefix}/users",
                'target' => '_self',
                'icon_class' => 'voyager-person',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 3,
                'created_at' => '2016-05-31 22:19:16',
                'updated_at' => '2016-05-31 22:20:07',
            ),
            4 => 
            array (
                'id' => 5,
                'menu_id' => 2,
                'title' => 'Categories',
                'url' => "/{$prefix}/categories",
                'target' => '_self',
                'icon_class' => 'voyager-categories',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 8,
                'created_at' => '2016-05-31 22:19:38',
                'updated_at' => '2016-06-01 20:07:46',
            ),
            5 => 
            array (
                'id' => 6,
                'menu_id' => 2,
                'title' => 'Pages',
                'url' => "/{$prefix}/pages",
                'target' => '_self',
                'icon_class' => 'voyager-file-text',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 7,
                'created_at' => '2016-05-31 22:20:03',
                'updated_at' => '2016-06-01 16:51:43',
            ),
            6 => 
            array (
                'id' => 7,
                'menu_id' => 2,
                'title' => 'Roles',
                'url' => "/{$prefix}/roles",
                'target' => '_self',
                'icon_class' => 'voyager-lock',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 2,
                'created_at' => '2016-10-21 19:14:25',
                'updated_at' => '2016-10-24 00:44:07',
            ),
        ));
        
        
    }
}
