<?php

namespace TCG\Voyager\Plugins\Interfaces;

use Illuminate\View\View;

interface WidgetInterface
{
    public function registerProtectedRoutes();

    public function registerPublicRoutes();

    public function getSettingsView(): ?View;

    public function getInstructionsView(): ?View;

    public function getWidgetView(): View;

    public function getWidth(): int;
}
