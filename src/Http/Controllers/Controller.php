<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use TCG\Voyager\Exceptions\JsonInvalidException;
use TCG\Voyager\Facades\Bread as BreadFacade;
use TCG\Voyager\Facades\Plugins as PluginsFacade;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\Plugins\AuthenticationPlugin;
use TCG\Voyager\Plugins\AuthorizationPlugin;

abstract class Controller extends BaseController
{
    public function authorize($ability, $arguments = [])
    {
        return $this->getAuthorizationPlugin()->each(function ($plugin) use ($ability, $arguments) {
            $plugin->authorize($ability, $arguments);
        });
    }

    protected function getAuthorizationPlugin()
    {
        return PluginsFacade::getPluginsByType('authorization');
    }

    protected function getAuthenticationPlugin()
    {
        return PluginsFacade::getPluginByType('authentication', AuthenticationPlugin::class);
    }
}
