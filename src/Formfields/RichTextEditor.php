<?php

namespace TCG\Voyager\Formfields;

class RichTextEditor extends BaseFormfield
{
    public $type = 'rich-text-editor';

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield.rich_text_editor');
        $this->options['placeholder'] = '';
        $this->options['disabled'] = false;
    }
}
