<?php

namespace TCG\Voyager\FormFields;

class RadioBtnHandler extends AbstractHandler
{
    protected $name = 'Radio Button';
    protected $codename = 'radio_btn';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.radio_btn', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
