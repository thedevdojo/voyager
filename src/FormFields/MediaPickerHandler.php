<?php

namespace TCG\Voyager\FormFields;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MediaPickerHandler extends AbstractHandler
{
    protected $codename = 'media_picker';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        $content = '';
        if (isset($options->max) && $options->max > 1) {
            if (is_array($dataTypeContent->{$row->field})) {
                $dataTypeContent->{$row->field} = json_encode($dataTypeContent->{$row->field});
            }
            json_decode($dataTypeContent->{$row->field});
            if (json_last_error() == JSON_ERROR_NONE) {
                $content = json_encode($dataTypeContent->{$row->field});
            } else {
                $content = json_encode('[]');
            }
        } else {
            $content = "'".$dataTypeContent->{$row->field}."'";
        }

        if (isset($options->base_path)) {
            $options->base_path = str_replace('{uid}', Auth::user()->getKey(), $options->base_path);
            if (Str::contains($options->base_path, '{date:')) {
                $options->base_path = preg_replace_callback('/\{date:([^\/\}]*)\}/', function ($date) {
                    return \Carbon\Carbon::now()->format($date[1]);
                }, $options->base_path);
            }
            if (Str::contains($options->base_path, '{random:')) {
                $options->base_path = preg_replace_callback('/\{random:([0-9]+)\}/', function ($random) {
                    return Str::random($random[1]);
                }, $options->base_path);
            }
            if (!$dataTypeContent->getKey()) {
                $uuid = (string) Str::uuid();
                $options->base_path = str_replace('{pk}', $uuid, $options->base_path);
                \Session::put($dataType->slug.'_path', $options->base_path);
                \Session::put($dataType->slug.'_uuid', $uuid);
            } else {
                $options->base_path = str_replace('{pk}', $dataTypeContent->getKey(), $options->base_path);
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
