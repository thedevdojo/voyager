<?php

namespace TCG\Voyager\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use TCG\Voyager\Facades\Voyager;
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

        return !empty($this->getTranslatableAttributes());
    }

    /**
     * Load translations relation.
     *
     * @return mixed
     */
    public function translations()
    {
        return $this->hasMany(Voyager::model('Translation'), 'foreign_key', $this->getKeyName())
            ->where('table_name', $this->getTable())
            ->whereIn('locale', config('voyager.multilingual.locales', []));
    }

    /**
     * This scope eager loads the translations for the default and the fallback locale only.
     * We can use this as a shortcut to improve performance in our application.
     *
     * @param Builder     $query
     * @param string|null $locale
     * @param string|bool $fallback
     */
    public function scopeWithTranslation(Builder $query, $locale = null, $fallback = true)
    {
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        if ($fallback === true) {
            $fallback = config('app.fallback_locale', 'en');
        }

        $query->with(['translations' => function (Relation $query) use ($locale, $fallback) {
            $query->where(function ($q) use ($locale, $fallback) {
                $q->where('locale', $locale);

                if ($fallback !== false) {
                    $q->orWhere('locale', $fallback);
                }
            });
        }]);
    }

    /**
     * This scope eager loads the translations for the default and the fallback locale only.
     * We can use this as a shortcut to improve performance in our application.
     *
     * @param Builder           $query
     * @param string|null|array $locales
     * @param string|bool       $fallback
     */
    public function scopeWithTranslations(Builder $query, $locales = null, $fallback = true)
    {
        if (is_null($locales)) {
            $locales = app()->getLocale();
        }

        if ($fallback === true) {
            $fallback = config('app.fallback_locale', 'en');
        }

        $query->with(['translations' => function (Relation $query) use ($locales, $fallback) {
            if (is_null($locales)) {
                return;
            }

            $query->where(function ($q) use ($locales, $fallback) {
                if (is_array($locales)) {
                    $q->whereIn('locale', $locales);
                } else {
                    $q->where('locale', $locales);
                }

                if ($fallback !== false) {
                    $q->orWhere('locale', $fallback);
                }
            });
        }]);
    }

    /**
     * Translate the whole model.
     *
     * @param null|string $language
     * @param bool|string $fallback
     *
     * @return Translator
     */
    public function translate($language = null, $fallback = true)
    {
        if (!$this->relationLoaded('translations')) {
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
     *
     * @return null
     */
    public function getTranslatedAttribute($attribute, $language = null, $fallback = true)
    {
        // If multilingual is not enabled don't check for translations
        if (!config('voyager.multilingual.enabled')) {
            return $this->getAttributeValue($attribute);
        }

        list($value) = $this->getTranslatedAttributeMeta($attribute, $language, $fallback);

        return $value;
    }

    public function getTranslationsOf($attribute, array $languages = null, $fallback = true)
    {
        if (is_null($languages)) {
            $languages = config('voyager.multilingual.locales', [config('voyager.multilingual.default')]);
        }

        $response = [];
        foreach ($languages as $language) {
            $response[$language] = $this->getTranslatedAttribute($attribute, $language, $fallback);
        }

        return $response;
    }

    public function getTranslatedAttributeMeta($attribute, $locale = null, $fallback = true)
    {
        // Attribute is translatable
        //
        if (!in_array($attribute, $this->getTranslatableAttributes())) {
            return [$this->getAttribute($attribute), config('voyager.multilingual.default'), false];
        }

        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        if ($fallback === true) {
            $fallback = config('app.fallback_locale', 'en');
        }

        $default = config('voyager.multilingual.default');

        if ($default == $locale) {
            return [$this->getAttribute($attribute), $default, true];
        }

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translations = $this->getRelation('translations')
            ->where('column_name', $attribute);

        $localeTranslation = $translations->where('locale', $locale)->first();

        if ($localeTranslation) {
            return [$localeTranslation->value, $locale, true];
        }

        if ($fallback == $locale) {
            return [$this->getAttribute($attribute), $locale, false];
        }

        if ($fallback == $default) {
            return [$this->getAttribute($attribute), $locale, false];
        }

        $fallbackTranslation = $translations->where('locale', $fallback)->first();

        if ($fallbackTranslation && $fallback !== false) {
            return [$fallbackTranslation->value, $locale, true];
        }

        return [null, $locale, false];
    }

    /**
     * Get attributes that can be translated.
     *
     * @return array
     */
    public function getTranslatableAttributes()
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }

    public function setAttributeTranslations($attribute, array $translations, $save = false)
    {
        $response = [];

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $default = config('voyager.multilingual.default', 'en');
        $locales = config('voyager.multilingual.locales', [$default]);

        foreach ($locales as $locale) {
            if (empty($translations[$locale])) {
                continue;
            }

            if ($locale == $default) {
                $this->$attribute = $translations[$locale];
                continue;
            }

            $tranlator = $this->translate($locale, false);
            $tranlator->$attribute = $translations[$locale];

            if ($save) {
                $tranlator->save();
            }

            $response[] = $tranlator;
        }

        return $response;
    }

    /**
     * Get entries filtered by translated value.
     *
     * @example  Class::whereTranslation('title', '=', 'zuhause', ['de', 'iu'])
     * @example  $query->whereTranslation('title', '=', 'zuhause', ['de', 'iu'])
     *
     * @param string       $field    {required} the field your looking to find a value in.
     * @param string       $operator {required} value you are looking for or a relation modifier such as LIKE, =, etc.
     * @param string       $value    {optional} value you are looking for. Only use if you supplied an operator.
     * @param string|array $locales  {optional} locale(s) you are looking for the field.
     * @param bool         $default  {optional} if true checks for $value is in default database before checking translations.
     *
     * @return Builder
     */
    public static function scopeWhereTranslation($query, $field, $operator, $value = null, $locales = null, $default = true)
    {
        if ($locales && !is_array($locales)) {
            $locales = [$locales];
        }
        if (!isset($value)) {
            $value = $operator;
            $operator = '=';
        }

        $self = new static();
        $table = $self->getTable();

        return $query->whereIn(
            $self->getKeyName(),
            Translation::where('table_name', $table)
            ->where('column_name', $field)
            ->where('value', $operator, $value)
            ->when(!is_null($locales), function ($query) use ($locales) {
                return $query->whereIn('locale', $locales);
            })
            ->pluck('foreign_key')
        )->when($default, function ($query) use ($field, $operator, $value) {
            return $query->orWhere($field, $operator, $value);
        });
    }

    public function hasTranslatorMethod($name)
    {
        if (!isset($this->translatorMethods)) {
            return false;
        }

        return isset($this->translatorMethods[$name]);
    }

    public function getTranslatorMethod($name)
    {
        if (!$this->hasTranslatorMethod($name)) {
            return;
        }

        return $this->translatorMethods[$name];
    }

    public function deleteAttributeTranslations(array $attributes, $locales = null)
    {
        $this->translations()
            ->whereIn('column_name', $attributes)
            ->when(!is_null($locales), function ($query) use ($locales) {
                $method = is_array($locales) ? 'whereIn' : 'where';

                return $query->$method('locale', $locales);
            })
            ->delete();
    }

    public function deleteAttributeTranslation($attribute, $locales = null)
    {
        $this->translations()
            ->where('column_name', $attribute)
            ->when(!is_null($locales), function ($query) use ($locales) {
                $method = is_array($locales) ? 'whereIn' : 'where';

                return $query->$method('locale', $locales);
            })
            ->delete();
    }

    /**
     * Prepare translations and set default locale field value.
     *
     * @param object $request
     *
     * @return array translations
     */
    public function prepareTranslations($request)
    {
        $translations = [];

        // Translatable Fields
        $transFields = $this->getTranslatableAttributes();

        $fields = !empty($request->attributes->get('breadRows')) ? array_intersect($request->attributes->get('breadRows'), $transFields) : $transFields;

        foreach ($fields as $field) {
            if (!$request->input($field.'_i18n')) {
                throw new Exception('Invalid Translatable field'.$field);
            }

            $trans = json_decode($request->input($field.'_i18n'), true);

            // Set the default local value
            $request->merge([$field => $trans[config('voyager.multilingual.default', 'en')]]);

            $translations[$field] = $this->setAttributeTranslations(
                $field,
                $trans
            );

            // Remove field hidden input
            unset($request[$field.'_i18n']);
        }

        // Remove language selector input
        unset($request['i18n_selector']);

        return $translations;
    }

    /**
     * Prepare translations and set default locale field value.
     *
     * @param object $requestData
     *
     * @return array translations
     */
    public function prepareTranslationsFromArray($field, &$requestData)
    {
        $translations = [];

        $field = 'field_display_name_'.$field;

        if (empty($requestData[$field.'_i18n'])) {
            throw new Exception('Invalid Translatable field '.$field);
        }

        $trans = json_decode($requestData[$field.'_i18n'], true);

        // Set the default local value
        $requestData['display_name'] = $trans[config('voyager.multilingual.default', 'en')];

        $translations['display_name'] = $this->setAttributeTranslations(
            'display_name',
            $trans
        );

        // Remove field hidden input
        unset($requestData[$field.'_i18n']);

        return $translations;
    }

    /**
     * Save translations.
     *
     * @param object $translations
     *
     * @return void
     */
    public function saveTranslations($translations)
    {
        foreach ($translations as $field => $locales) {
            foreach ($locales as $locale => $translation) {
                $translation->save();
            }
        }
    }
}
