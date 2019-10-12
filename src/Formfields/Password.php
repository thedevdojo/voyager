<?php

namespace TCG\Voyager\Formfields;

class Password extends BaseFormfield
{
    public $type = 'password';

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield_password');
        $this->options['placeholder'] = '';
        $this->options['disabled'] = false;
    }

    /**
     * Transform data to be stored in the database after updating.
     *
     * @param mixed $data The input-data
     * @param mixed $old  The old data
     *
     * @return mixed The processed data
     */
    public function update($data, $old)
    {
        if (empty($data)) {
            return $old;
        }

        return $this->store($data, $old);
    }

    /**
     * Transform data to be stored in the database after adding.
     *
     * @param mixed $data The input-data
     * @param mixed $old  The old data (null)
     *
     * @return mixed The processed data
     */
    public function store($data, $old)
    {
        return bcrypt($data);
    }
}
