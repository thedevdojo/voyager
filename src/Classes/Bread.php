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
    public $model_name;
    public $controller;
    public $policy;
    public $layouts = [];

    protected $parse_failed = false;

    public function __construct($path)
    {
        $content = File::get($path);
        $json = @json_decode($content);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Voyager::flashMessage('BREAD-file "'.basename($path).'" does contain invalid JSON: '.json_last_error_msg(), 'debug');
            $this->parse_failed = true;

            return;
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
        return app($this->model_name);
    }

    public function getFields()
    {
        return DB::getSchemaBuilder()->getColumnListing($this->table);
    }

    public function getAccessors()
    {
        return $this->getModel()->accessors ?? [];
    }

    public function getRelationships($deep = false)
    {
        $relationships = $this->getModel()->relationships ?? [];
        if ($deep) {
            foreach ($relationships as $key => $name) {
                $relationship = $this->getModel()->{$name}();
                $table = $relationship->getRelated()->getTable();
                unset($relationships[$key]);
                $relationships[$name] = [
                    'bread'  => Voyager::getBread($table),
                    'fields' => Voyager::getBread($table)->getFields(),
                    'type'   => basename(get_class($relationship)),
                ];
            }
        }

        return $relationships;
    }

    public function getLayoutFor($action)
    {
        // TODO: Get layout based on action and roles
        if ($action == 'browse') {
            return $this->layouts->filter(function ($layout) {
                return $layout->type == 'list';
            })->first();
        }

        return $this->layouts[0];
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
            'model_name'    => $this->model_name,
            'controller'    => $this->controller,
            'policy'        => $this->policy,
            'layouts'       => $this->layouts,
        ];
    }
}
