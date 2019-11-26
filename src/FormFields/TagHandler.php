<?php

namespace TCG\Voyager\FormFields;

class TagHandler extends AbstractHandler
{
    protected $codename = 'tag';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.tag', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
