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
     * @param mixed  $data The input-data
     * @param mixed  $old  The old data
     * @param string $type The type of the table-column (eg. text, int, varchar, etc)
     *
     * @return mixed The processed data
     */
    public function update($data, $old, $type = null)
    {
        if (empty($data)) {
            return $old;
        }

        return $this->store($data, $old, $type);
    }

    /**
     * Transform data to be stored in the database after adding.
     *
     * @param mixed  $data The input-data
     * @param mixed  $old  The old data (null)
     * @param string $type The type of the table-column (eg. text, int, varchar, etc)
     *
     * @return mixed The processed data
     */
    public function store($data, $old, $type = null)
    {
        return bcrypt($data);
    }
}
