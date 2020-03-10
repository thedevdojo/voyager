<?php

namespace TCG\Voyager\Plugins;

use Illuminate\View\View;
use TCG\Voyager\Plugins\Interfaces\AuthorizationInterface;

class AuthorizationPlugin implements AuthorizationInterface
{
    public function authorize($ability, $arguments = [])
    {
        // Do nothing means allow everyone to do everything
    }

    public function registerProtectedRoutes()
    {
        //
    }

    public function registerPublicRoutes()
    {
        return null;
    }

    public function getSettingsView(): ?View
    {
        return null;
    }

    public function getInstructionsView(): ?View
    {
        return null;
    }
}
