<?php

namespace TCG\Voyager\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use TCG\Voyager\Facades\Voyager;
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

    public function __construct($path, $parameter = null)
    {
        $json = [];
        if ($path) {
            $content = File::get($path);
            $json = @json_decode($content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Voyager::flashMessage('BREAD-file "'.basename($path).'" does contain invalid JSON: '.json_last_error_msg(), 'debug');
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
                Voyager::flashMessage('One layout in the "'.basename($path).'" BREAD is invalid!', 'debug');
            }
        }
    }

    public function getModel()
    {
        return app($this->model);
    }

    public function getFields()
    {
        return DB::getSchemaBuilder()->getColumnListing($this->table);
    }

    public function getComputedProperties()
    {
        if (property_exists($this->getModel(), 'computed')) {
            return $this->getModel()->computed;
        }

        return [];
    }

    public function getTranslatableFields($deep = true)
    {
        $translatable = collect([]);
        if (property_exists($this->getModel(), 'translatable')) {
            $translatable = collect($this->getModel()->translatable);
        }

        if ($deep) {
            $relationships = $this->getRelationships($deep);
            foreach ($relationships as $name => $relationship) {
                collect($relationship['bread']->getTranslatableFields(false))->each(function ($field) use ($name, $translatable) {
                    $translatable->push($name.'.'.$field);
                });
            }
        }

        return $translatable;
    }

    public function isFieldTranslatable($field)
    {
        return $this->getTranslatableFields()->contains($field);
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
                if (get_class($relationship) == \Illuminate\Database\Eloquent\Relations\BelongsToMany::class) {
                    $pivot = DB::getSchemaBuilder()->getColumnListing($relationship->getTable());
                    $pivot = array_diff($pivot, [
                        $relationship->getForeignPivotKeyName(),
                        $relationship->getRelatedPivotKeyName(),
                    ]);
                }
                $relationships[$name] = [
                    'bread'  => Voyager::getBread($table),
                    'fields' => Voyager::getBread($table)->getFields(),
                    'type'   => basename(get_class($relationship)),
                    'pivot'  => array_values($pivot),
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
        } elseif ($action == 'edit') {
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
