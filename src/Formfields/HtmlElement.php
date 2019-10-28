<?php

namespace TCG\Voyager\Formfields;

class HtmlElement extends BaseFormfield
{
    public $type = 'html-element';
    public $lists = false;
    public $settings = false;

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield.html_element');
        $this->options['type'] = 'hr';
        $this->options['content'] = '';
    }

    public function edit($data, $model)
    {
        return [];
    }

    public function show($data, $model)
    {
        return [];
    }
}
