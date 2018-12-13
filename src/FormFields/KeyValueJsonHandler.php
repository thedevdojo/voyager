<?php

namespace TCG\Voyager\FormFields;

class KeyValueJsonHandler extends AbstractHandler
{
    protected $codename = 'key_value_json';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.key_value_json', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
    
}