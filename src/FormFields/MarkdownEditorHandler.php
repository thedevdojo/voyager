<?php

namespace TCG\Voyager\FormFields;

class MarkdownEditorHandler extends AbstractHandler
{
    protected $codename = 'markdown_editor';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('voyager::formfields.markdown_editor', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
