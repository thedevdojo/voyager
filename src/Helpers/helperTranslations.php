<?php

if (!function_exists('isFieldTranslatable')) {
    /**
     * Check if a Field is translatable.
     *
     * @param Illuminate\Database\Eloquent\Model      $model
     * @param Illuminate\Database\Eloquent\Collection $row
     */
    function isFieldTranslatable($model, $row)
    {
        if (!isBreadTranslatable($model)) {
            return;
        }

        return isset($model['translatable']) &&
            in_array($row->field, $model['translatable']);
    }
}

if (!function_exists('getFieldTranslations')) {
    /**
     * Return all field translations.
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @param string                             $field
     * @param string                             $rowType
     * @param bool                               $stripHtmlTags
     */
    function getFieldTranslations($model, $field, $rowType = '', $stripHtmlTags = false)
    {
        $_out = $model->getTranslationsOf($field);

        if ($stripHtmlTags && $rowType == 'rich_text_box') {
            foreach ($_out as $language => $value) {
                $_out[$language] = strip_tags($_out[$language]);
            }
        }

        return json_encode($_out);
    }
}

if (!function_exists('isBreadTranslatable')) {
    /**
     * Check if BREAD is translatable.
     *
     * @param Illuminate\Database\Eloquent\Model $model
     */
    function isBreadTranslatable($model)
    {
        return config('voyager.multilingual.bread')
            && isset($model, $model['translatable']);
    }
}
