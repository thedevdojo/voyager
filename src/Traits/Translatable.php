<?php

namespace TCG\Voyager\Traits;

trait Translatable
{
    public function __construct()
    {
        $this->current_locale = app()->getLocale();
        $this->fallback_locale = config('app.fallback_locale');
    }

    public function __get($key)
    {
        $value = $this->{$key};

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
                $this->{$key} = $value;
            } elseif (is_array($this->{$key})) {
                $this->{$key}[$this->current_locale] = $value;
            } elseif (is_object($this->{$key})) {
                $this->{$key}->{$this->current_locale} = $value;
            } else {
                // TODO: Save as object or array?
            }
        } else {
            $this->{$key} = $value;
        }
    }
}
