<?php

namespace TCG\Voyager\FormFields;

class MediaPickerHandler extends AbstractHandler
{
    protected $codename = 'media_picker';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        $content = '';
        if (isset($options->max) && $options->max == 1) {
            $content = "'".$dataTypeContent->{$row->field}."'";
        } else {
            if (is_array($dataTypeContent->{$row->field})) {
                $dataTypeContent->{$row->field} = json_encode($dataTypeContent->{$row->field});
            }
            json_decode($dataTypeContent->{$row->field});
            if (json_last_error() == JSON_ERROR_NONE) {
                $content = json_encode($dataTypeContent->{$row->field});
            } else {
                $content = json_encode('[]');
            }
        }

        return view('voyager::formfields.media_picker', [
            'row'      => $row,
            'options'  => $options,
            'dataType' => $dataType,
            'content'  => $content,
        ]);
    }
}
