<?php

namespace TCG\Voyager\Events;

use Illuminate\Queue\SerializesModels;
use TCG\Voyager\Models\Menu;

class MenuDisplay
{
    use SerializesModels;

    public $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;

        // Deprecate on v1.3
        //
        event('voyager.menu.display', $menu);
    }
}
