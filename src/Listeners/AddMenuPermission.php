<?php

namespace TCG\Voyager\Listeners;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Events\BreadAdded;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class AddMenuPermission
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Will create Menu item and Permission after creating a BREAD item.
     *
     * @param  BreadAdded $event
     *
     * @return void
     */
    public function handle(BreadAdded $bread)
    {
        $this->addPermission($bread);

        $this->addMenu($bread);
    }


    private function addMenu($bread)
    {
        if (config('voyager.autoadd_menu_permission') && file_exists(base_path('routes/web.php'))) {
            require base_path('routes/web.php');

            $menu = Menu::where('name', 'admin')->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title'   => $bread->dataType->display_name_plural,
                'url'     => '/'.config('voyager.prefix', 'admin').'/'.$bread->dataType->slug,
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target'     => '_self',
                    'icon_class' => $bread->dataType->icon,
                    'color'      => null,
                    'parent_id'  => null,
                    'order'      => $order,
                ])->save();
            }
        }
    }

    /**
     * Add Permission for BREAD.
     *
     * @param [type] $bread [description]
     */
    private function addPermission($bread)
    {
        // Create permission
        //
        // Permission::generateFor(snake_case($bread->dataType->slug));

        $role = Role::where('name', 'admin')->firstOrFail();

        // Assign permission to admin
        //
        $permissions = Permission::where(['table_name' => $bread->dataType->name])->get()->pluck('id')->all();

        $role->permissions()->attach($permissions);
    }
}
