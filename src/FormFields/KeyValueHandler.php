<?php

namespace TCG\Voyager\FormFields;

class KeyValueHandler extends AbstractHandler
{
    protected $codename = 'key_value';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.key_value', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
