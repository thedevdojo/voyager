<?php

namespace TCG\Voyager\Traits;

use TCG\Voyager\Models\Translation;
use TCG\Voyager\Translator;

trait Translatable
{
    /**
     * Check if this model can translate.
     *
     * @return bool
     */
    public function translatable()
    {
        if (isset($this->translatable) && $this->translatable == false) {
            return false;
        }

        return ! empty($this->getTranslatableAttributes());
    }

    /**
     * Load translations relation.
     *
     * @return mixed
     */
    public function translations()
    {
        return $this->hasMany(Translation::class, 'foreign_key', $this->getKeyName())
            ->where('table_name', $this->getTable())
            ->whereIn('locale', config('voyager.multilingual.locales', []));
    }

    /**
     * Translate the whole model.
     *
     * @param null $language
     * @param bool $fallback
     */
    public function translate($language = null, $fallback = true)
    {
        if ($this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return (new Translator($this))->translate($language, $fallback);
    }

    /**
     * Get a single translated attribute.
     *
     * @param $attribute
     * @param null $language
     * @param bool $fallback
     * @return null
     */
    public function getTranslatedAttribute($attribute, $language = null, $fallback = true)
    {
        if (! in_array($attribute, $this->getTranslatableAttributes())) {
            return $this->getAttribute($attribute);
        }

        if ($this->relationLoaded('translations')) {
            $this->load('translations');
        }

        if (is_null($language)) {
            $language = app()->getLocale();
        }

        if ($fallback === true) {
            $fallback = config('app.fallback_locale', 'en');
        }

        $default = config('voyager.multilingual.default');

        $translations = $this->getRelation('translations')
            ->where('column_name', $attribute);

        if ($default == $language) {
            return $this->getAttribute($attribute);
        }

        $languageTranslation = $translations->where('locale', $language)->first();

        if ($languageTranslation) {
            return $languageTranslation->value;
        }

        if ($fallback == $language) {
            return $this->getAttribute($attribute);
        }

        $fallbackTranslation = $translations->where('locale', $fallback)->first();

        if ($fallbackTranslation && $fallback !== false) {
            return $fallbackTranslation->value;
        }

        return null;
    }

    /**
     * Get attributes that can be translated.
     *
     * @return array
     */
    public function getTranslatableAttributes()
    {
        return isset($this->translate) ? $this->translate : [];
    }
}