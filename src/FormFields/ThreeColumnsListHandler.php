<?php

namespace TCG\Voyager\FormFields;

class ThreeColumnsListHandler extends AbstractHandler
{
    protected $codename = 'three_columns_list';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.three_columns_list', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
