<?php

namespace TCG\Voyager\Traits;

use TCG\Voyager\Models\Translation;

trait Translateable
{
    protected $translated = false;

    protected $originalAttributes = [];

    public function translations()
    {
        return $this->hasMany(Translation::class, 'foreign_key', $this->getKeyName())
            ->where('table_name', $this->getTable())
            ->whereIn('locale', config('voyager.multilingual.locales', []));
    }

    public function translate($language = null, $fallback = true)
    {
        if ($this->relationLoaded('translations')) {
            $this->load('translations');
        }

        if (is_null($language)) {
            $language = app()->getLocale();
        }

        if ($fallback === true) {
            $fallback = app()->getLocale();
        }

        if ($language == config('voyager.multilingual.default')) {
            if ($this->translated) {
                $this->setRawAttributes($this->originalAttributes);
            }

            return $this;
        }

        if (!$this->translated) {
            $this->originalAttributes = $this->getAttributes();
        }

        foreach ($this->translate as $attribute) {
            $this->setAttribute(
                $attribute,
                $this->getTranslatedAttribute($attribute, $language, $fallback)
            );
        }

        $this->translated = true;

        return $this;
    }

    public function getTranslatedAttribute($attribute, $language = null, $fallback = true)
    {
        if ($this->relationLoaded('translations')) {
            $this->load('translations');
        }

        if (is_null($language)) {
            $language = app()->getLocale();
        }

        if ($fallback === true) {
            $fallback = app()->getLocale();
        }

        $default = config('voyager.multilingual.default');

        $translations = $this->getRelation('translations')
            ->where('column_name', $attribute);

        if ($default == $language) {
            if ($this->translated) {
                return $this->originalAttributes[$attribute];
            }

            return $this->getAttribute($attribute);
        }

        $languageTranslation = $translations->where('locale', $language)->first();

        if ($languageTranslation) {
            return $languageTranslation->value;
        }

        if ($fallback == $language) {
            if ($this->translated) {
                return $this->originalAttributes[$attribute];
            }

            return $this->getAttribute($attribute);
        }

        $fallbackTranslation = $translations->where('locale', $fallback)->first();

        if ($fallbackTranslation) {
            return $fallbackTranslation->value;
        }

        return null;
    }
}