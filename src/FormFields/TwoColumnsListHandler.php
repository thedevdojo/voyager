<?php

namespace TCG\Voyager\FormFields;

class TwoColumnsListHandler extends AbstractHandler
{
    protected $codename = 'two_columns_list';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.two_columns_list', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
