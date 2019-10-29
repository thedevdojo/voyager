<?php

namespace TCG\Voyager\Traits;

trait Translatable
{
    public $translate = true;

    public function __construct()
    {
        $this->current_locale = app()->getLocale();
        $this->fallback_locale = config('app.fallback_locale');
    }

    public function __get($key)
    {
        $value = null;

        if ($this instanceof \Illuminate\Database\Eloquent\Model) {
            $value = $this->getAttribute($key);
        } else {
            $value = $this->{$key};
        }

        if (!$this->translate) {
            return $value;
        }

        if (property_exists($this, 'translatable') && is_array($this->translatable) && in_array($key, $this->translatable)) {
            $current_locale = app()->getLocale();
            $fallback_locale = config('app.fallback_locale');
            if (is_string($value)) {
                $json = @json_decode($value);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $value = $json;
                }
            }

            if (is_array($value)) {
                return $value[$current_locale] ?? $value[$fallback_locale] ?? '';
            } elseif (is_object($value)) {
                return $value->{$current_locale} ?? $value->{$fallback_locale} ?? '';
            }
        }

        return $value;
    }

    public function __set($key, $value)
    {
        if (property_exists($this, 'translatable') && is_array($this->translatable) && in_array($key, $this->translatable)) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            } else {
                $value = json_encode((object) [
                    $this->current_locale => $value,
                ]);
            }

            if (in_array($key, $this->casts)) {
                if ($this->casts[$key] == 'object') {
                    // TODO:
                } elseif ($this->casts[$key] == 'array') {
                    // TODO:
                } elseif ($this->casts[$key] == 'collection') {
                    // TODO:
                }
            }
        }
        if ((bool)class_parents($this)) {
            parent::__set($key, $value);
        } else {
            $this->{$key} = $value;
        }
    }
}
