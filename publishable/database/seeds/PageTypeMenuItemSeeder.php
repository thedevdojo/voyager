<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class PageTypeMenuItemSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        if (file_exists(base_path('routes/web.php'))) {
            require base_path('routes/web.php');

            $menu = Menu::where('name', 'admin')->firstOrFail();

            $menu_item = MenuItem::firstOrNew([
                'menu_id'    => $menu->id,
                'title'      => 'Page Types',
                'url'        => route('voyager.page_types.index', [], false),
            ]);
            if (!$menu_item->exists) {
                $menu_item->fill([
                    'target'     => '_self',
                    'icon_class' => 'voyager-window-list',
                    'color'      => null,
                    'parent_id'  => null,
                    'order'      => 7,
                ])->save();
            }
        }
    }
}
