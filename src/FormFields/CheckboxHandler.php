<?php

namespace TCG\Voyager\FormFields;

class CheckboxHandler extends AbstractHandler
{
    protected $codename = 'checkbox';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.checkbox', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
