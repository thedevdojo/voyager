<?php

namespace TCG\Voyager\Plugins\Interfaces;

use Illuminate\View\View;

interface BaseInterface
{
    public function registerProtectedRoutes();

    public function registerPublicRoutes();

    public function getSettingsView(): ?View;

    public function getInstructionsView(): ?View;
}
