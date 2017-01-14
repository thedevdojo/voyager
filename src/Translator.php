<?php

namespace TCG\Voyager;

use ArrayAccess;
use Illuminate\Database\Eloquent\Model;

class Translator implements ArrayAccess
{
    protected $model;
    protected $attributes = [];
    protected $locale;

    public function __construct(Model $model)
    {
        if (!$model->relationLoaded('translations')) {
            $model->load('translations');
        }

        $this->model = $model;
        $this->locale = config('voyager.multilingual.default', 'en');
        $attributes = [];

        foreach ($this->model->getAttributes() as $attribute => $value) {
            $attributes[$attribute] = [
                'value'  => $value,
                'locale' => $this->locale,
                'exists' => true,
                'modified' => false,
            ];
        }

        $this->attributes = $attributes;
    }

    public function translate($locale = null, $fallback = true)
    {
        $this->locale = $locale;

        foreach ($this->model->getTranslatableAttributes() as $attribute) {
            $this->translateAttribute($attribute, $locale, $fallback);
        }

        return $this;
    }

    protected function translateAttribute($attribute, $locale = null, $fallback = true)
    {
        list($value, $locale, $exists) = $this->model->getTranslatedAttributeMeta($attribute, $locale, $fallback);

        $this->attributes[$attribute] = [
            'value' => $value,
            'locale' => $locale,
            'exists' => $exists,
            'modified' => false,
        ];

        return $this;
    }

    protected function translateAttributeToOriginal($attribute)
    {
        $this->attributes[$attribute] = [
            'value'  => $this->model->attributes[$attribute],
            'locale' => config('voyager.multilingual.default', 'en'),
            'exists' => true,
            'modified' => false,
        ];

        return $this;
    }

    public function __get($name)
    {
        return $this->attributes[$name]['value'];
    }

    public function __set($name, $value)
    {
        $this->attributes[$name]['value'] = $value;
        $this->attributes[$name]['modified'] = true;
    }

    public function offsetGet($offset)
    {
        return $this->attributes[$offset]['value'];
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset]['value'] = $value;
        $this->attributes[$offset]['modified'] = true;
    }

    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}
