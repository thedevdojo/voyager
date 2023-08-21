<?php

if (!function_exists('is_field_translatable')) {
    /**
     * Check if a Field is translatable.
     *
     * @param Illuminate\Database\Eloquent\Model      $model
     * @param mixed Illuminate\Database\Eloquent\Collection | string $row
     */
    function is_field_translatable($model, $row)
    {
        if (!is_bread_translatable($model)) {
            return;
        }

        $field = is_string($row) ? $row : $row->field;

        return $model->translatable()
            && method_exists($model, 'getTranslatableAttributes')
            && in_array($field, $model->getTranslatableAttributes());
    }
}

if (!function_exists('get_field_translations')) {
    /**
     * Return all field translations.
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @param string                             $field
     * @param string                             $rowType
     * @param bool                               $stripHtmlTags
     */
    function get_field_translations($model, $field, $rowType = '', $stripHtmlTags = false)
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
