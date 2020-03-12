<?php

namespace TCG\Voyager\Classes;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
    public $icon = 'window';
    public $model;
    public $controller;
    public $policy;
    public $scope;
    public $global_search_field;
    public $ajax_validation = true;
    public $layouts = [];

    public $parse_failed = false;

    protected $model_class = null;
    protected $reflection_class = null;

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

    public function getReflectionClass()
    {
        if (!class_exists($this->model)) {
            return;
        }

        if (!$this->reflection_class) {
            $this->reflection_class = new \ReflectionClass($this->model);
        }

        return $this->reflection_class;
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

    public function getScopes()
    {
        if (!$this->getReflectionClass()) {
            return [];
        }
        return collect($this->getReflectionClass()->getMethods())->filter(function ($method) {
            return Str::startsWith($method->name, 'scope');
        })->whereNotIn('name', ['scopeWithTranslations', 'scopeWithTranslation', 'scopeWhereTranslation'])->transform(function ($method) {
            return lcfirst(Str::replaceFirst('scope', '', $method->name));
        });
    }

    public function getRelationships()
    {
        if (!$this->model) {
            return [];
        }
        $relationship_types = [
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            \Illuminate\Database\Eloquent\Relations\HasOne::class,
        ];
        $relationships = collect($this->getReflectionClass()->getMethods())->filter(function ($method) use ($relationship_types) {
            return in_array(strval($method->getReturnType()), $relationship_types);
        })->pluck('name')->toArray();

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
                'pivot'   => array_values($pivot),
            ];
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
            'table'               => $this->table,
            'slug'                => $this->slug,
            'name_singular'       => $this->name_singular,
            'name_plural'         => $this->name_plural,
            'icon'                => $this->icon,
            'model'               => $this->model,
            'controller'          => $this->controller,
            'policy'              => $this->policy,
            'scope'               => $this->scope,
            'global_search_field' => $this->global_search_field,
            'ajax_validation'     => $this->ajax_validation,
            'layouts'             => $this->layouts,
        ];
    }
}
