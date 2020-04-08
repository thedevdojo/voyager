<?php

namespace TCG\Voyager\Contracts\Plugins;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

interface IsMenuPlugin extends IsGenericPlugin
{
    public function getMenuView(): ?View;
}
