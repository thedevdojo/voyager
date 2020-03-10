<?php

namespace TCG\Voyager\Formfields;

class Password extends BaseFormfield
{
    public $type = 'password';
    public $translatable = false;

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield.password');
        $this->options['placeholder'] = '';
        $this->options['disabled'] = false;
    }

    public function update($data, $old, $model, $request_data)
    {
        if (empty($data)) {
            return [
                $this->column => $old,
            ];
        }

        return $this->store($data, $old, $model, $request_data);
    }

    public function store($data, $old, $model, $request_data)
    {
        return [
            $this->column => bcrypt($data),
        ];
    }
}
