<?php

namespace TCG\Voyager\FormFields;

class DateHandler extends AbstractHandler
{
    protected $codename = 'date';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view(config('voyager.views.formfields.date', false), [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
