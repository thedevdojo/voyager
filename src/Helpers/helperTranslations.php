<?php

if (!function_exists('is_field_translatable')) {
    /**
     * Check if a Field is translatable.
     *
     * @param Illuminate\Database\Eloquent\Model      $model
     * @param Illuminate\Database\Eloquent\Collection $row
     */
    function is_field_translatable($model, $row)
    {
        if (!is_bread_translatable($model)) {
            return;
        }

        return $model->translatable()
            && method_exists($model, 'getTranslatableAttributes')
            && in_array($row->field, $model->getTranslatableAttributes());
    }
}

if (!function_exists('get_field_translations')) {
    /**
     * Return all field translations.
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @param string                             $field
     * @param bool                               $stripHtmlTags
     * @param string|null                        $allowableHtmlTags
     * @param int|null                           $maxLength
     */
    function get_field_translations($model, $field, $stripHtmlTags = false, $allowableHtmlTags = null, $maxLength = null)
    {
        $_out = $model->getTranslationsOf($field);

        if ($stripHtmlTags) {
            foreach ($_out as $language => $value) {
                $_out[$language] = strip_tags($_out[$language], $allowableHtmlTags ?: null);
            }
        }

        if ($maxLength) {
            foreach ($_out as $language => $value) {
                $_out[$language] = str_limit_html($_out[$language], $maxLength);
            }
        }

        return json_encode($_out);
    }
}

if (!function_exists('is_bread_translatable')) {
    /**
     * Check if BREAD is translatable.
     *
     * @param Illuminate\Database\Eloquent\Model $model
     */
    function is_bread_translatable($model)
    {
        return config('voyager.multilingual.enabled')
            && isset($model)
            && method_exists($model, 'translatable')
            && $model->translatable();
    }
}
