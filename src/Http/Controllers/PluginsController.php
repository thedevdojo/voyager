<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

class PluginsController extends Controller
{
    public function enable(Request $request)
    {
        $identifier = $request->get('identifier');
        if ($request->get('enable', false)) {
            return VoyagerFacade::enablePlugin($identifier);
        }

        return VoyagerFacade::disablePlugin($identifier);
    }

    public function get()
    {
        return VoyagerFacade::getAllPlugins()->sortBy('identifier')->transform(function ($plugin) {
            // This is only used to preview a theme
            if ($plugin->type == 'theme') {
                $plugin->src = $plugin->getStyleRoute();
            }

            return $plugin;
        });
    }

    public function settings($key)
    {
        $plugin = VoyagerFacade::getAllPlugins()->get($key);
        if (!$plugin) {
            throw new \TCG\Voyager\Exceptions\PluginNotFoundException('This Plugin does not exist');
        } elseif ($plugin->has_settings && $plugin->enabled) {
            return $plugin->getSettingsView();
        }

        return redirect()->back();
    }
}
