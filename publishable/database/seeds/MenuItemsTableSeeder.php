<?php

use TCG\Voyager\Models\Menu;
use Illuminate\Database\Seeder;
use TCG\Voyager\Models\MenuItem;

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

            $menu = Menu::where('name', 'admin')->first();

            // Dashboard
            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'url'     => route('voyager.dashboard', [], false),
            ]);
            if (!$menuItem->exists) {
                $menuItem->fill([
                    'title' => 'Dashboard',
                    'icon_class' => 'voyager-boat',
                    'order' => 1,
                ])->save();
            }

            // Roles
            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'url'     => route('roles.index', [], false),
            ]);
            if (!$menuItem->exists) {
                $menuItem->fill([
                    'title' => 'Roles',
                    'icon_class' => 'voyager-lock',
                    'order' => 2,
                ])->save();
            }

            // Users
            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'url'     => route('users.index', [], false),
            ]);
            if (!$menuItem->exists) {
                $menuItem->fill([
                    'title'      => 'Users',
                    'icon_class' => 'voyager-person',
                    'order'      => 3,
                ])->save();
            }

            // Media
            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'url'     => route('voyager.media', [], false),
            ]);
            if (!$menuItem->exists) {
                $menuItem->fill([
                    'title'      => 'Media',
                    'icon_class' => 'voyager-images',
                    'order'      => 5,
                ])->save();
            }

            // Posts
            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'url'     => route('posts.index', [], false),
            ]);
            if (!$menuItem->exists) {
                $menuItem->fill([
                    'title'      => 'Posts',
                    'icon_class' => 'voyager-news',
                    'order'      => 6,
                ])->save();
            }

            // Pages
            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'url'     => route('pages.index', [], false),
            ]);
            if (!$menuItem->exists) {
                $menuItem->fill([
                    'title'      => 'Pages',
                    'icon_class' => 'voyager-file-text',
                    'order'      => 7,
                ])->save();
            }

            // Categories
            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'url'     => route('categories.index', [], false),
            ]);
            if (!$menuItem->exists) {
                $menuItem->fill([
                    'title'      => 'Categories',
                    'icon_class' => 'voyager-categories',
                    'order'      => 8,
                ])->save();
            }
        }
    }
}
