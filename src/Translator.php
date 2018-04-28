<?php

namespace TCG\Voyager;

use ArrayAccess;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;

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
                'value'    => $value,
                'locale'   => $this->locale,
                'exists'   => true,
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

    /**
     * Save changes made to the translator attributes.
     *
     * @return bool
     */
    public function save()
    {
        $attributes = $this->getModifiedAttributes();
        $savings = [];

        foreach ($attributes as $key => $attribute) {
            if ($attribute['exists']) {
                $translation = $this->getTranslationModel($key);
            } else {
                $translation = VoyagerFacade::model('Translation')->where('table_name', $this->model->getTable())
                    ->where('column_name', $key)
                    ->where('foreign_key', $this->model->getKey())
                    ->where('locale', $this->locale)
                    ->first();
            }

            if (is_null($translation)) {
                $translation = VoyagerFacade::model('Translation');
            }

            $translation->fill([
                'table_name'  => $this->model->getTable(),
                'column_name' => $key,
                'foreign_key' => $this->model->getKey(),
                'value'       => $attribute['value'],
                'locale'      => $this->locale,
            ]);

            $savings[] = $translation->save();

            $this->attributes[$key]['locale'] = $this->locale;
            $this->attributes[$key]['exists'] = true;
            $this->attributes[$key]['modified'] = false;
        }

        return in_array(false, $savings);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getRawAttributes()
    {
        return $this->attributes;
    }

    public function getOriginalAttributes()
    {
        return $this->model->getAttributes();
    }

    public function getOriginalAttribute($key)
    {
        return $this->model->getAttribute($key);
    }

    public function getTranslationModel($key, $locale = null)
    {
        return $this->model->getRelation('translations')
            ->where('column_name', $key)
            ->where('locale', $locale ? $locale : $this->locale)
            ->first();
    }

    public function getModifiedAttributes()
    {
        return collect($this->attributes)->where('modified', 1)->all();
    }

    protected function translateAttribute($attribute, $locale = null, $fallback = true)
    {
        list($value, $locale, $exists) = $this->model->getTranslatedAttributeMeta($attribute, $locale, $fallback);

        $this->attributes[$attribute] = [
            'value'    => $value,
            'locale'   => $locale,
            'exists'   => $exists,
            'modified' => false,
        ];

        return $this;
    }

    protected function translateAttributeToOriginal($attribute)
    {
        $this->attributes[$attribute] = [
            'value'    => $this->model->attributes[$attribute],
            'locale'   => config('voyager.multilingual.default', 'en'),
            'exists'   => true,
            'modified' => false,
        ];

        return $this;
    }

    public function __get($name)
    {
        if (!isset($this->attributes[$name])) {
            if (isset($this->model->$name)) {
                return $this->model->$name;
            }

            return;
        }

        if (!$this->attributes[$name]['exists'] && !$this->attributes[$name]['modified']) {
            return $this->getOriginalAttribute($name);
        }

        return $this->attributes[$name]['value'];
    }

    public function __set($name, $value)
    {
        $this->attributes[$name]['value'] = $value;

        if (!in_array($name, $this->model->getTranslatableAttributes())) {
            return $this->model->$name = $value;
        }

        $this->attributes[$name]['modified'] = true;
    }

    public function offsetGet($offset)
    {
        return $this->attributes[$offset]['value'];
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset]['value'] = $value;

        if (!in_array($offset, $this->model->getTranslatableAttributes())) {
            return $this->model->$offset = $value;
        }

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

    public function getLocale()
    {
        return $this->locale;
    }

    public function translationAttributeExists($name)
    {
        if (!isset($this->attributes[$name])) {
            return false;
        }

        return $this->attributes[$name]['exists'];
    }

    public function translationAttributeModified($name)
    {
        if (!isset($this->attributes[$name])) {
            return false;
        }

        return $this->attributes[$name]['modified'];
    }

    public function createTranslation($key, $value)
    {
        if (!isset($this->attributes[$key])) {
            return false;
        }

        if (!in_array($key, $this->model->getTranslatableAttributes())) {
            return false;
        }

        $translation = VoyagerFacade::model('Translation');
        $translation->fill([
            'table_name'  => $this->model->getTable(),
            'column_name' => $key,
            'foreign_key' => $this->model->getKey(),
            'value'       => $value,
            'locale'      => $this->locale,
        ]);
        $translation->save();

        $this->model->getRelation('translations')->add($translation);

        $this->attributes[$key]['exists'] = true;
        $this->attributes[$key]['value'] = $value;

        return $this->model->getRelation('translations')
            ->where('key', $key)
            ->where('locale', $this->locale)
            ->first();
    }

    public function createTranslations(array $translations)
    {
        foreach ($translations as $key => $value) {
            $this->createTranslation($key, $value);
        }
    }

    public function deleteTranslation($key)
    {
        if (!isset($this->attributes[$key])) {
            return false;
        }

        if (!$this->attributes[$key]['exists']) {
            return false;
        }

        $translations = $this->model->getRelation('translations');
        $locale = $this->locale;

        VoyagerFacade::model('Translation')->where('table_name', $this->model->getTable())
            ->where('column_name', $key)
            ->where('foreign_key', $this->model->getKey())
            ->where('locale', $locale)
            ->delete();

        $this->model->setRelation('translations', $translations->filter(function ($translation) use ($key, $locale) {
            return $translation->column_name != $key && $translation->locale != $locale;
        }));

        $this->attributes[$key]['value'] = null;
        $this->attributes[$key]['exists'] = false;
        $this->attributes[$key]['modified'] = false;

        return true;
    }

    public function deleteTranslations(array $keys)
    {
        foreach ($keys as $key) {
            $this->deleteTranslation($key);
        }
    }

    public function __call($method, array $arguments)
    {
        if (!$this->model->hasTranslatorMethod($method)) {
            throw new \Exception('Call to undefined method TCG\Voyager\Translator::'.$method.'()');
        }

        return call_user_func_array([$this, 'runTranslatorMethod'], [$method, $arguments]);
    }

    public function runTranslatorMethod($method, array $arguments)
    {
        array_unshift($arguments, $this);

        $method = $this->model->getTranslatorMethod($method);

        return call_user_func_array([$this->model, $method], $arguments);
    }
}
