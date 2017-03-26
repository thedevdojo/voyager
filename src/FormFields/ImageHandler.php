<?php

namespace TCG\Voyager\FormFields;

class ImageHandler extends AbstractHandler
{
    protected $codename = 'image';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view(config('voyager.views.formfields.image', false), [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
