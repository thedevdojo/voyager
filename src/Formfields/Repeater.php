<?php

namespace TCG\Voyager\Formfields;

class Repeater extends BaseFormfield
{
    public $type = 'repeater';
    public $translatable = false; // The repeater itself cannot be translated
    public $lists = false;
    public $settings = false;

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield.repeater');
        $this->options['formfields'] = [];
        $this->options['add_text'] = __('voyager::generic.add_new_element');
    }

    public function update($data, $old, $model, $request_data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        return [
            $this->column => $data,
        ];
    }

    public function store($data, $old, $model, $request_data)
    {
        return $this->update($data, $old, $model, $request_data);
    }
}
