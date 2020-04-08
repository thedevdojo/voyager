<?php

namespace TCG\Voyager\Contracts\Plugins;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

interface IsThemePlugin extends IsGenericPlugin
{
    public function getStyleRoute(): string;
}
