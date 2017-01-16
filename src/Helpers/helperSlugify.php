<?php

if (!function_exists('isBreadSlugAutoGenerator')) {
    /**
     * Check if a slug field can be auto generated.
     *
     * @param json $options
     *
     * @return string HTML output.
     */
    function isBreadSlugAutoGenerator($options)
    {
        if (isset($options->slugify)) {
            return ' data-slug-origin='.$options->slugify->origin
                   .((isset($options->slugify->forceUpdate))
                      ? ' data-slug-forceupdate=true'
                      : '');
        }
    }
}

if (!function_exists('isFieldSlugAutoGenerator')) {
    /**
     * Determine the details field, for a given dataTypeContent.
     *
     * @param Illuminate\Database\Eloquent\Collection $dataTypeContent
     * @param string                                  $field
     *
     * @return string HTML output.
     */
    function isFieldSlugAutoGenerator($dataTypeContent, $field)
    {
        if (! isset($dataType)) {
            return;
        }

        $_tmp = (isset($dataTypeContent->id))
                      ? $dataType->editRows
                      : $dataType->addRows;

        $_tmp = $_tmp->filter(function ($val) {
            return $val->field == $field;
        })->first()->details;

        return isBreadTranslatable(json_decode($_tmp));
    }
}
