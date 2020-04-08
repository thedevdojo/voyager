<?php

namespace TCG\Voyager\Contracts\Plugins;

use Illuminate\View\View;

interface IsWidgetPlugin extends IsGenericPlugin
{
    public function getWidgetView(): View;

    public function getWidth(): int;
}
