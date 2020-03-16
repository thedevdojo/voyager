<?php

namespace TCG\Voyager\Plugins\Interfaces;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

interface MenuInterface extends GenericInterface
{
    public function getMenuView(): ?View;
}
