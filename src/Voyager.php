<?php

namespace TCG\Voyager;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TCG\Voyager\Classes\Bread as BreadClass;

class Voyager
{
    protected $breads;
    protected $breadPath;
    protected $actions;
    protected $formfields;
    protected $messages = [];

    /**
     * Get Voyagers routes.
     *
     * @return array an array of routes
     */
    public function routes()
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
        if (!$this->breads) {
            //$this->breads = Cache::rememberForever('voyager-breads', function () {
            if (!File::isDirectory($this->breadPath)) {
                File::makeDirectory($this->breadPath);
            }
            $this->breads = collect(File::files($this->breadPath))->transform(function ($bread) {
                return new BreadClass($bread->getPathName());
            })->filter(function ($bread) {
                if (!$bread->parse_failed && !$bread->isValid()) {
                    $this->flashMessage('BREAD "'.$bread->slug.'" is not valid!', 'debug');
                }

                return $bread->isValid();
            });
            //});
        }

        return $this->breads;
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
        if (!$this->breads) {
            $this->getBreads();
        }

        return $this->breads->where('table', $table)->first();
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
        if (!$this->breads) {
            $this->getBreads();
        }

        return $this->breads->filter(function ($bread) use ($slug) {
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
        return File::put(Str::finish($this->breadPath, '/').$bread->table.'.json', json_encode($bread, JSON_PRETTY_PRINT));
    }

    /**
     * Create a BREAD-file.
     *
     * @param string $table
     *
     * @return int|bool success
     */
    public function createBread($table)
    {
        $bread = [
            'table'         => $table,
            'slug'          => (object) [],
            'name_singular' => (object) [],
            'name_plural'   => (object) [],
            'layouts'       => (object) [],

        ];

        // TODO: Validate if BREAD already exists and throw exception?

        return $this->storeBread((object) $bread);
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
        return collect(['bread', 'manager', 'generic'])->flatMap(function ($file) {
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
     * @param string $bread The BREAD
     *
     * @return object The actions
     */
    public function getActionsForBread($bread)
    {
        return $this->actions->filter(function ($action) use ($bread) {
            $action->bread = $bread;

            return $action->shouldBeDisplayOnBread($bread);
        });
    }
}
