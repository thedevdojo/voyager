<?php

namespace TCG\Voyager\Observers;

use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class MenuObserver
{
    public function created($item)
    {
        if (get_class($item) == MenuItem::class) {
            $this->updated($item);
        }
    }

    public function updated($item)
    {
        if (get_class($item) == MenuItem::class) {
            $item = $item->menu;
        }
        $item->removeMenuFromCache();
    }

    public function deleted($item)
    {
        $this->updated($item);
    }
}