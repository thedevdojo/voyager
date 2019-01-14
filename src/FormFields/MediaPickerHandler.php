<?php

namespace TCG\Voyager\FormFields;

class MediaPickerHandler extends AbstractHandler
{
    protected $codename = 'media_picker';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.media_picker', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
