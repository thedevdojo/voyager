<?php

namespace TCG\Voyager\Contracts\Plugins;

use Illuminate\View\View;

interface IsMenuPlugin extends IsGenericPlugin
{
    public function getMenuView(): ?View;
}
