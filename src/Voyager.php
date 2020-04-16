<?php

namespace TCG\Voyager;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Facades\Plugins as PluginsFacade;

class Voyager
{
    protected $messages = [];
    protected $tables = [];

    /**
     * Get Voyagers routes.
     *
     * @return array an array of routes
     */
    public function routes()
    {
        PluginsFacade::launchPlugins();
        require __DIR__.'/../routes/voyager.php';
    }

    /**
     * Generate an absolute URL for an asset-file.
     *
     * @param string $path the relative path, e.g. js/voyager.js
     *
     * @return string
     */
    public function assetUrl($path)
    {
        return route('voyager.voyager_assets').'?path='.urlencode($path);
    }

    /**
     * Flash a message to the UI.
     *
     * @param string $message The message
     * @param string $color   The tailwind color of the exception: blue, yellow, green, red...
     * @param bool   $next    If the message should be flashed after the next request
     */
    public function flashMessage($message, $color, $timeout = 5000, $next = false)
    {
        $this->messages[] = [
            'message' => $message,
            'color'   => $color,
            'timeout' => $timeout,
        ];
        if ($next) {
            session()->push('voyager-messages', [
                'message' => $message,
                'color'   => $color,
                'timeout' => $timeout,
            ]);
        }
    }

    /**
     * Get all messages.
     *
     * @return array The messages
     */
    public function getMessages()
    {
        $messages = array_merge($this->messages, session()->get('voyager-messages', []));
        session()->forget('voyager-messages');

        return collect($messages)->unique();
    }

    /**
     * Get all Voyager translation strings.
     *
     * @return array The language strings
     */
    public function getLocalization()
    {
        return collect(['auth', 'bread', 'generic', 'manager', 'plugins', 'validation'])->flatMap(function ($file) {
            return ['voyager::'.$file => trans('voyager::'.$file)];
        })->toJson();
    }

    /**
     * Get all Routes.
     *
     * @return array The routes
     */
    public function getRoutes()
    {
        return collect(\Route::getRoutes())->mapWithKeys(function ($route) {
            return [$route->getName() => url($route->uri())];
        })->filter(function ($value, $key) {
            return $key != '';
        });
    }

    public function getRoutePrefix()
    {
        return $this->route_prefix;
    }

    /**
     * Get all tables in the database.
     *
     * @return array
     */
    public function getTables()
    {
        return DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }

    public function getColumns($table)
    {
        if (!array_key_exists($table, $this->tables)) {
            $builder = DB::getSchemaBuilder();
            $this->tables[$table] = $builder->getColumnListing($table);
        }

        return $this->tables[$table];
    }

    /**
     * Get all locales supported by the app.
     *
     * @return array The locales
     */
    public function getLocales()
    {
        return config('app.locales', [$this->getLocale()]);
    }

    /**
     * Get the current app-locale.
     *
     * @return string The current locale
     */
    public function getLocale()
    {
        return app()->getLocale();
    }

    /**
     * Get the current app-locale.
     *
     * @return string The current locale
     */
    public function getFallbackLocale()
    {
        return config('app.fallback_locale', [$this->getLocale()]);
    }

    /**
     * Get wether the app is translatable or not.
     *
     * @return bool Wether the app is translatable or not.
     */
    public function isTranslatable()
    {
        return count($this->getLocales()) > 1;
    }

    /**
     * Validate if all locales of a translation array are not empty.
     *
     * @param array $data The translation array
     *
     * @return bool Wether a locale is empty or not.
     */
    public function validateAllLocales($data)
    {
        return $this->validateLocales($data, $this->getLocales());
    }

    /**
     * Validate if the given locales of a translation array are not empty.
     *
     * @param array $data   The translation array
     * @param array $locale The locales to test again
     *
     * @return bool Wether a locale is empty or not.
     */
    public function validateLocales($data, $locales)
    {
        if (!is_array($data) || !$this->isTranslatable()) {
            return !empty($data);
        }

        foreach ($locales as $locale) {
            if (!property_exists($data, $locale) || empty($data[$locale])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets all widgets from installed and enabled plugins
     *
     * @return Collection The widgets
     */
    public function getWidgets()
    {
        // TODO: Cache widgets?

        return collect(PluginsFacade::getPluginsByType('widget')->where('enabled')->transform(function ($plugin) {
            $width = $plugin->getWidth();
            if ($width >= 1 && $width <= 11) {
                $width = 'w-'.$width.'/12';
            } else {
                $width = 'w-full';
            }
            return (object)[
                'width' => $width,
                'view'  => $plugin->getWidgetView()
            ];
        }));
    }
}
