<?php

namespace TCG\Voyager\Plugins\Interfaces;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

interface ThemeInterface extends BaseInterface
{
    public function getStyleRoute(): string;
}
