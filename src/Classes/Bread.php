<?php

namespace TCG\Voyager\Classes;

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
    public $layouts;

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
            $this->{$key} = $data;
        }
    }

    public function getModel()
    {
        if ($this->model_name) {
            return app($this->model_name);
        }
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
        return [];
    }
}
