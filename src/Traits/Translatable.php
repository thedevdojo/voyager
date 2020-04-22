<?php

namespace TCG\Voyager\Traits;

trait Translatable
{
    public $translate = true;
    public $current_locale;
    public $fallback_locale;

    public function __construct()
    {
        $this->current_locale = app()->getLocale();
        $this->fallback_locale = config('app.fallback_locale');
    }

    public function getTranslated($key, $locale, $fallback, $default)
    {
        $old_locale = $this->current_locale;
        $old_fallback = $this->fallback_locale;

        // Set locales to desired
        $this->current_locale = $locale;
        $this->fallback_locale = $fallback;

        $value = $this->__get($key);

        // Set locales back to original
        $this->current_locale = $old_locale;
        $this->fallback_locale = $old_fallback;

        return $value ?? $default;
    }

    public function setTranslated($key, $value, $locale)
    {
        $old_locale = $this->current_locale;

        // Set locale to desired
        $this->current_locale = $locale;

        $this->__set($key, $value);

        // Set locales back to original
        $this->current_locale = $old_locale;
    }

    public function whereTranslated($query)
    {
        // TODO: ...

        return $query;
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
            if (is_string($value)) {
                $json = @json_decode($value);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $value = $json;
                }
            }

            if (is_array($value)) {
                return $value[$this->current_locale] ?? $value[$this->fallback_locale] ?? null;
            } elseif (is_object($value)) {
                return $value->{$this->current_locale} ?? $value->{$this->fallback_locale} ?? null;
            }
        }

        return $value;
    }

    public function __set($key, $value)
    {
        if ($this->translate && property_exists($this, 'translatable') && is_array($this->translatable) && in_array($key, $this->translatable)) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            } else {
                $value = json_encode((object) [
                    $this->current_locale => $value,
                ]);
            }
        }
        if ((bool) class_parents($this)) {
            parent::__set($key, $value);
        } else {
            $this->{$key} = $value;
        }
    }
}
