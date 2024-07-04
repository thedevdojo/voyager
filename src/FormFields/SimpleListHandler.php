<?php

namespace TCG\Voyager\FormFields;

class SimpleListHandler extends AbstractHandler
{
    protected $codename = 'simple_list';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.simple_list', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
