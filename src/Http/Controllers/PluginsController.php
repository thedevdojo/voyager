<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use TCG\Voyager\Facades\Plugins as PluginsFacade;

class PluginsController extends Controller
{
    public function enable(Request $request)
    {
        $identifier = $request->get('identifier');
        if ($request->get('enable', false)) {
            return PluginsFacade::enablePlugin($identifier);
        }

        return PluginsFacade::disablePlugin($identifier);
    }

    public function get()
    {
        return PluginsFacade::getAllPlugins()->sortBy('identifier')->transform(function ($plugin) {
            // This is only used to preview a theme
            if ($plugin->type == 'theme') {
                $plugin->src = $plugin->getStyleRoute();
            }

            return $plugin;
        });
    }

    public function settings($key)
    {
        $plugin = PluginsFacade::getAllPlugins()->get($key);
        if (!$plugin) {
            throw new \TCG\Voyager\Exceptions\PluginNotFoundException('This Plugin does not exist');
        } elseif ($plugin->has_settings && $plugin->enabled) {
            return $plugin->getSettingsView();
        }

        return redirect()->back();
    }
}
