<?php

namespace TCG\Voyager\Plugins\Interfaces;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

interface ThemeInterface extends GenericInterface
{
    public function getStyleRoute(): string;
}
