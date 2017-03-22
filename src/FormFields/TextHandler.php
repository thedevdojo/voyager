<?php

namespace TCG\Voyager\FormFields;

class TextHandler extends AbstractHandler
{
    protected $codename = 'text';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view(config('voyager.views.formfields.text', false), [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
