<?php

namespace TCG\Voyager\Plugins\Interfaces;

use Illuminate\View\View;

interface WidgetInterface extends GenericInterface
{
    public function getWidgetView(): View;

    public function getWidth(): int;
}
