<?php

namespace TCG\Voyager\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use TCG\Voyager\Plugins\BasePlugin;

trait Plugins
{
    protected $plugins;
    protected $enabled_plugins;
    protected $path;

    public function pluginsPath($path)
    {
        $this->path = $path;
    }

    public function addPlugin($plugin = null)
    {
        if (!$this->plugins) {
            $this->plugins = collect();
        }
        if (!$this->enabled_plugins) {
            $this->loadEnabledPlugins();
        }
        if ($plugin) {
            if (!is_object($plugin)) {
                $plugin = new $plugin();
            }
            $plugin->type = $this->getPluginType($plugin);

            $plugin->identifier = $plugin->repository .'@'. class_basename($plugin);
            $plugin->enabled = in_array($plugin->identifier, $this->enabled_plugins);
            if ($plugin->getInstructionsView()) {
                $plugin->instructions = $plugin->getInstructionsView()->render();
            }
            $plugin->has_settings = !is_null($plugin->getSettingsView());
            $this->plugins->push($plugin);
        }
    }

    public function loadEnabledPlugins()
    {
        $this->enabled_plugins = [];

        $folder = dirname($this->path);
        if (!File::isDirectory($folder)) {
            File::makeDirectory($folder);
        }
        if (!File::exists($this->path)) {
            File::put($this->path, '[]');
        }

        collect(@json_decode(File::get($this->path)))->where('enabled')->each(function ($plugin) {
            $this->enabled_plugins[] = $plugin->identifier;
        });
    }

    public function getAllPlugins()
    {
        return collect($this->plugins);
    }

    public function launchPlugins()
    {
        $this->getAllPlugins()->each(function ($plugin, $key) {
            if ($plugin->enabled || $plugin->type == 'theme') {
                $plugin->registerPublicRoutes();
                Route::group(['middleware' => 'voyager.admin'], function () use ($plugin, $key) {
                    $plugin->registerProtectedRoutes();
                });
            }
        });
    }

    public function getPluginByType($type, $fallback = null)
    {
        $plugin = $this->getPluginsByType($type)->where('enabled')->first();
        if (!$plugin && $fallback !== null) {
            $plugin = $fallback;
            if (!($fallback instanceof BasePlugin)) {
                $plugin = new $fallback();
            }
        }

        return $plugin;
    }

    public function getPluginsByType($type)
    {
        return $this->getAllPlugins()->where('type', $type);
    }

    public function getAvailablePlugins()
    {
        return @json_decode(File::get(realpath(__DIR__.'/../../plugins.json')));
    }

    public function enablePlugin($identifier, $enable = true)
    {
        $this->getAllPlugins();

        $plugins = collect(@json_decode(File::get($this->path)));
        if (!$plugins->contains('identifier', $identifier)) {
            $plugins->push([
                'identifier' => $identifier,
                'enabled'    => $enable,
            ]);
        } else {
            $plugins->where('identifier', $identifier)->first()->enabled = $enable;
        }

        return File::put($this->path, json_encode($plugins, JSON_PRETTY_PRINT));
    }

    public function disablePlugin($identifier)
    {
        return $this->enablePlugin($identifier, false);
    }

    protected function getPluginType($class)
    {
        return collect(class_implements($class))->filter(function ($interface) {
            return Str::startsWith($interface, 'TCG\\Voyager\\Plugins\\Interfaces\\');
        })->transform(function ($interface) {
            return strtolower(str_replace(['TCG\\Voyager\\Plugins\\Interfaces\\', 'Interface'], '', $interface));
        })->first();
    }
}
