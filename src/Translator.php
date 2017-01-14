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
        if (!in_array($attribute, $this->model->getTranslatableAttributes())) {
            return $this->model->getAttribute($attribute);
        }

        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        if ($fallback === true) {
            $fallback = config('app.fallback_locale', 'en');
        }

        $default = config('voyager.multilingual.default', 'en');

        if ($default == $locale) {
            return $this->translateAttributeToOriginal($attribute);
        }

        $localeTranslation = $this->model->getRelation('translations')
            ->where('column_name', $attribute)
            ->where('locale', $locale)
            ->first();

        if ($localeTranslation) {
            $this->attributes[$attribute] = [
                'value'  => $localeTranslation->value,
                'locale' => $localeTranslation->locale,
                'exists' => true,
            ];

            return $this;
        }

        if ($default == $fallback) {
            return $this->translateAttributeToOriginal($attribute);
        }

        $fallbackTranslation = $this->model->getRelation('translations')
            ->where('column_name', $attribute)
            ->where('locale', $locale)
            ->first();

        if ($fallbackTranslation && $fallback !== false) {
            $this->attributes[$attribute] = [
                'value'  => $fallbackTranslation->value,
                'locale' => $fallbackTranslation->locale,
                'exists' => true,
            ];

            return $this;
        }

        $this->attributes[$attribute] = [
            'value'  => null,
            'locale' => null,
            'exists' => false,
        ];

        return $this;
    }

    protected function translateAttributeToOriginal($attribute)
    {
        $this->attributes[$attribute] = [
            'value'  => $this->model->attributes[$attribute],
            'locale' => config('voyager.multilingual.default', 'en'),
            'exists' => true,
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
    }

    public function offsetGet($offset)
    {
        return $this->attributes[$offset]['value'];
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset]['value'] = $value;
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
