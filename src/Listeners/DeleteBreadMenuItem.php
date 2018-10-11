<?php

namespace TCG\Voyager\Listeners;

use TCG\Voyager\Events\BreadDeleted;
use TCG\Voyager\Models\MenuItem;

class DeleteBreadMenuItem
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
     * Delete a MenuItem for a given BREAD.
     *
     * @param BreadDeleted $bread
     *
     * @return void
     */
    public function handle(BreadDeleted $bread)
    {
        if (config('voyager.bread.add_menu_item') && file_exists(base_path('routes/web.php'))) {
            $menuItem = MenuItem::where(['title' => $bread->dataType->display_name_plural]);

            if ($menuItem->exists()) {
                $menuItem->delete();
            }
        }
    }
}
