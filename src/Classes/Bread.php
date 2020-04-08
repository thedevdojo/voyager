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
    public $layouts = [];

    protected $model_class = null;

    public function __construct($json)
    {
        foreach ($json as $key => $value) {
            $this->{$key} = $value;
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
            'layouts'             => $this->layouts,
        ];
    }
}
