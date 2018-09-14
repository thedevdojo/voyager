<?php

namespace TCG\Voyager\FormFields;

class LinkHandler extends AbstractHandler
{
    protected $codename = 'link';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.link', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
