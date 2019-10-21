<?php

namespace TCG\Voyager\Classes;

use \Illuminate\Database\Eloquent\Relations\BelongsToMany;
use \Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use TCG\Voyager\Facades\Bread as BreadFacade;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\Traits\Translatable;

class Bread implements \JsonSerializable
{
    use Translatable;

    private $translatable = ['slug', 'name_singular', 'name_plural'];

    public $table;
    protected $slug;
    protected $name_singular;
    protected $name_plural;
    public $model;
    public $controller;
    public $policy;
    public $layouts = [];

    public $parse_failed = false;

    protected $model_class = null;

    public function __construct($path, $parameter = null)
    {
        $json = [];
        if ($path) {
            $content = File::get($path);
            $json = @json_decode($content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                VoyagerFacade::flashMessage('BREAD-file "'.basename($path).'" does contain invalid JSON: '.json_last_error_msg(), 'debug');
                $this->parse_failed = true;

                return;
            }
        } else {
            $json = $parameter;
        }

        foreach ($json as $key => $data) {
            if ($key == 'layouts') {
                $this->parseLayouts($data, $path);
            } else {
                $this->{$key} = $data;
            }
        }
    }

    private function parseLayouts($data, $path)
    {
        $this->layouts = collect();
        foreach ($data ?? [] as $layout) {
            $layout = new Layout($layout, $this);
            if ($layout->isValid()) {
                $this->layouts->push($layout);
            } else {
                VoyagerFacade::flashMessage('One layout in the "'.basename($path).'" BREAD is invalid!', 'debug');
            }
        }
    }

    public function getModel()
    {
        if (!$this->model_class) {
            $this->model_class = app($this->model);
        }

        return $this->model_class;
    }

    public function usesSoftDeletes()
    {
        return in_array(SoftDeletes::class, class_uses($this->getModel()));
    }

    public function getColumns()
    {
        return VoyagerFacade::getColumns($this->table);
    }

    public function getComputedProperties()
    {
        if (property_exists($this->getModel(), 'computed')) {
            return $this->getModel()->computed;
        }

        return [];
    }

    public function getTranslatableColumns($deep = true)
    {
        $translatable = collect([]);
        if (property_exists($this->getModel(), 'translatable')) {
            $translatable = collect($this->getModel()->translatable);
        }

        if ($deep) {
            $relationships = $this->getRelationships($deep);
            foreach ($relationships as $name => $relationship) {
                if ($relationship['bread']) {
                    // TODO: &$translatable?
                    collect($relationship['bread']->getTranslatableColumns(false))->each(function ($column) use ($name, $translatable) {
                        $translatable->push($name.'.'.$column);
                    });
                }
            }
        }

        return $translatable;
    }

    public function isColumnTranslatable($column)
    {
        return $this->getTranslatableColumns()->contains($column);
    }

    public function getRelationships($deep = false)
    {
        $relationships = [];
        if (property_exists($this->getModel(), 'relationships')) {
            $relationships = $this->getModel()->relationships;
        }
        if ($deep) {
            foreach ($relationships as $key => $name) {
                $pivot = [];
                $relationship = $this->getModel()->{$name}();
                $table = $relationship->getRelated()->getTable();
                unset($relationships[$key]);
                if (get_class($relationship) == BelongsToMany::class) {
                    $pivot = VoyagerFacade::getColumns($relationship->getTable());
                    $pivot = array_diff($pivot, [
                        $relationship->getForeignPivotKeyName(),
                        $relationship->getRelatedPivotKeyName(),
                    ]);
                }
                $relationships[$name] = [
                    'bread'   => BreadFacade::getBread($table),
                    'columns' => VoyagerFacade::getColumns($table),
                    'type'    => basename(get_class($relationship)),
                    'pivot'   => $pivot,
                ];
            }
        }

        return $relationships;
    }

    public function getLayoutFor($action, $fail = true)
    {
        $layout = null;
        // TODO: Get layout based on action and roles
        if ($action == 'browse') {
            $layout = $this->layouts->filter(function ($layout) {
                return $layout->type == 'list';
            })->first();
        } elseif ($action == 'edit' || $action == 'add' || $action == 'read') {
            $layout = $this->layouts->filter(function ($layout) {
                return $layout->type == 'view';
            })->first();
        }

        if ($fail && !$layout) {
            throw new \TCG\Voyager\Exceptions\NoLayoutFoundException();
        }

        return $layout;
    }

    public function isValid()
    {
        if (is_null($this->table) || is_null($this->slug) || is_null($this->name_singular) || is_null($this->name_plural)) {
            return false;
        }

        return true;
    }

    public function jsonSerialize()
    {
        return [
            'table'         => $this->table,
            'slug'          => $this->slug,
            'name_singular' => $this->name_singular,
            'name_plural'   => $this->name_plural,
            'model'         => $this->model,
            'controller'    => $this->controller,
            'policy'        => $this->policy,
            'layouts'       => $this->layouts,
        ];
    }
}
