<?php

namespace TCG\Voyager\Formfields;

class BaseFormfield implements \JsonSerializable
{
    public $type;
    public $name;
    public $field;
    public $options = [
        'width'       => '1/2',
        'title'       => '',
        'description' => '',
    ];
    public $rules = [];

    /**
     * Transform data to be stored in the database after updating.
     *
     * @param mixed $data The input-data
     * @param mixed $old  The old data
     * @param string $type The type of the table-column (eg. text, int, varchar, etc)
     *
     * @return mixed The processed data
     */
    public function update($data, $old, $type = null)
    {
        return $data;
    }

    /**
     * Transform data to be stored in the database after adding.
     *
     * @param mixed $data The input-data
     * @param mixed $old  The old data (null)
     * @param string $type The type of the table-column (eg. text, integer, etc)
     *
     * @return mixed The processed data
     */
    public function store($data, $old, $type = null)
    {
        return $data;
    }

    /**
     * Check if the formfield is valid.
     *
     * @return bool Is the formfield valid
     */
    public function isValid()
    {
        return true;
    }

    public function jsonSerialize()
    {
        return [
            'type'    => $this->type,
            'field'   => $this->field,
            'options' => $this->options,
            'rules'   => $this->rules,
        ];
    }
}
