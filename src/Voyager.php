<?php

namespace TCG\Voyager;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TCG\Voyager\Classes\Bread as BreadClass;

class Voyager
{
    protected $breadPath;
    protected $actions;
    protected $formfields;
    protected $messages = [];
    protected $tables = [];

    /**
     * Get Voyagers routes.
     *
     * @return array an array of routes
     */
    public function routes(Router $router)
    {
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
     * Sets the path where the BREAD-files are stored.
     *
     * @param string $path
     *
     * @return string the current pat
     */
    public function breadPath($path = null)
    {
        if ($path) {
            $this->breadPath = Str::finish($path, '/');
        }

        return $this->breadPath;
    }

    /**
     * Get all BREADs from storage and validate.
     *
     * @return \TCG\Voyager\Classes\Bread
     */
    public function getBreads()
    {
        return Cache::rememberForever('voyager-breads', function () {
            if (!File::isDirectory($this->breadPath)) {
                File::makeDirectory($this->breadPath);
            }

            return collect(File::files($this->breadPath))->transform(function ($bread) {
                return new BreadClass($bread->getPathName());
            })->filter(function ($bread) {
                if (!$bread->parse_failed && !$bread->isValid()) {
                    $this->flashMessage('BREAD "'.$bread->slug.'" is not valid!', 'debug');
                }

                return $bread->isValid();
            });
        });
    }

    /**
     * Get a BREAD by the table name.
     *
     * @param string $table
     *
     * @return \TCG\Voyager\Classes\Bread
     */
    public function getBread($table)
    {
        return $this->getBreads()->where('table', $table)->first();
    }

    /**
     * Get a BREAD by the slug.
     *
     * @param string $slug
     *
     * @return \TCG\Voyager\Classes\Bread
     */
    public function getBreadBySlug($slug)
    {
        return $this->getBreads()->filter(function ($bread) use ($slug) {
            return $bread->slug == $slug;
        })->first();
    }

    /**
     * Store a BREAD-file.
     *
     * @param string $bread
     *
     * @return int|bool success
     */
    public function storeBread($bread)
    {
        $this->clearBreads();

        return File::put(Str::finish($this->breadPath, '/').$bread->table.'.json', json_encode($bread, JSON_PRETTY_PRINT));
    }

    /**
     * Create a BREAD-object.
     *
     * @param string $table
     *
     * @return int|bool success
     */
    public function createBread($table)
    {
        $bread = [
            'table'         => $table,
            'slug'          => Str::slug($table),
            'name_singular' => Str::singular(Str::title($table)),
            'name_plural'   => Str::plural(Str::title($table)),
            'layouts'       => (object) [],
        ];

        return new BreadClass(null, $bread);
    }

    /**
     * Clears all BREAD-objects.
     */
    public function clearBreads()
    {
        Cache::forget('voyager-breads');
    }

    /**
     * Delete a BREAD from the filesystem.
     *
     * @param string $table The table of the BREAD
     */
    public function deleteBread($table)
    {
        $this->clearBreads();

        return File::delete(Str::finish($this->breadPath, '/').$table.'.json');
    }

    /**
     * Flash a message to the UI.
     *
     * @param string $message The message
     * @param string $type    The type of the exception: info, success, warning, error or debug
     */
    public function flashMessage($message, $type)
    {
        $this->messages[] = [
            'message' => $message,
            'type'    => $type,
        ];
    }

    /**
     * Get all messages.
     *
     * @return array The messages
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get all Voyager translation strings.
     *
     * @return array The language strings
     */
    public function getLocalization()
    {
        return collect(['bread', 'generic', 'manager', 'validation'])->flatMap(function ($file) {
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
        return $this->formfields->unique();
    }

    /**
     * Get formfields-description.
     *
     * @return Illuminate\Support\Collection The formfields
     */
    public function getFormfieldsDescription()
    {
        return $this->getFormfields()->map(function ($formfield) {
            return [
                'type'    => $formfield->type,
                'name'    => $formfield->name,
                'options' => $formfield->options,
                'rules'   => $formfield->rules,
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

    public function getFields($table)
    {
        if (!array_key_exists($table, $this->tables)) {
            $builder = DB::getSchemaBuilder();
            $fields = $builder->getColumnListing($table);
            $this->tables[$table] = [];
            foreach ($fields as $field) {
                $this->tables[$table][$field] = $builder->getColumnType($table, $field);
            }
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
