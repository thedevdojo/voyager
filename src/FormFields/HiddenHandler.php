<?php

namespace TCG\Voyager\FormFields;

class HiddenHandler extends AbstractHandler
{
    protected $codename = 'hidden';

    public function createContent($row, $dataType, $dataTypeContent, $options, $action)
    {
        return view('voyager::formfields.hidden', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
            'action'          => $action,
        ]);
    }
}
