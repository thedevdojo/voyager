<?php

namespace TCG\Voyager;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Traits\Plugins;
use TCG\Voyager\Traits\Settings;

class Voyager
{
    use Plugins, Settings;

    protected $actions;
    protected $formfields;
    protected $messages = [];
    protected $tables = [];

    /**
     * Get Voyagers routes.
     *
     * @return array an array of routes
     */
    public function routes()
    {
        $this->launchPlugins();
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
     * @param string $type    The type of the exception: info, success, warning, error or debug
     * @param bool   $next    If the message should be flashed after the next request
     */
    public function flashMessage($message, $type, $next = false)
    {
        $this->messages[] = [
            'message' => $message,
            'type'    => $type,
        ];
        if ($next) {
            session()->push('voyager-messages', [
                'message' => $message,
                'type'    => $type,
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
     * Add a formfield.
     *
     * @param string $class The class of the formfield
     */
    public function addFormfield($class)
    {
        if (!$this->formfields) {
            $this->formfields = collect();
        }
        $class = new $class();
        $this->formfields->push($class);
    }

    /**
     * Get all formfields.
     *
     * @return Illuminate\Support\Collection The formfields
     */
    public function getFormfields()
    {
        return $this->formfields;
    }

    /**
     * Get formfields-description.
     *
     * @return Illuminate\Support\Collection The formfields
     */
    public function getFormfieldsDescription()
    {
        return $this->getFormfields()->sortBy('name')->map(function ($formfield) {
            return [
                'type'         => $formfield->type,
                'name'         => $formfield->name,
                'options'      => $formfield->options,
                'rules'        => $formfield->rules,
                'translatable' => $formfield->translatable,
                'lists'        => $formfield->lists,
                'views'        => $formfield->views,
                'settings'     => $formfield->settings,
            ];
        });
    }

    /**
     * Get a formfield by type.
     *
     * @param string $type The type of the formfield
     *
     * @return object The formfield
     */
    public function getFormfield($type)
    {
        return $this->formfields->filter(function ($formfield) use ($type) {
            return $formfield->type == $type;
        })->first();
    }

    /**
     * Add an action.
     *
     * @param string $class The class of the action
     */
    public function addAction($class)
    {
        if (!$this->actions) {
            $this->actions = collect();
        }
        $class = new $class();
        $this->actions->push($class);
    }

    /**
     * Get all actions.
     *
     * @return Illuminate\Support\Collection The actions
     */
    public function getActions()
    {
        return $this->actions->unique();
    }

    /**
     * Get all actions which should be shown on a BREAD.
     *
     * @param object $bread  The BREAD
     * @param object $layout The layout
     *
     * @return object The actions
     */
    public function getActionsForBread($bread, $layout)
    {
        return $this->actions->filter(function ($action) use ($bread, $layout) {
            $action->bread = $bread;
            $action->layout = $layout;

            return $action->shouldBeDisplayOnBread($bread);
        });
    }

    /**
     * Get all actions which should be shown on a BREAD.
     *
     * @param string $bread The BREAD
     *
     * @return object The actions
     */
    public function getActionsForEntry($bread, $layout, $entry)
    {
        return $this->actions->filter(function ($action) use ($bread, $layout, $entry) {
            $action->bread = $bread;
            $action->layout = $layout;

            return $action->shouldBeDisplayOnBread() && $action->shouldBeDisplayedOnEntry($entry);
        });
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
}
