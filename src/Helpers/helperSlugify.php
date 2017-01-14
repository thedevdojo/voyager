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
        $options = json_decode(
            ((isset($dataTypeContent->id))
                ? $dataType->editRows
                : $dataType->addRows
            )->filter(function ($val) {
                return $val->field == 'slug';
            })->first()->details
        );

        return isBreadTranslatable($options);
    }
}
