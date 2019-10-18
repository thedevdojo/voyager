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
     * Transform data to be shown when browsing.
     *
     * @param mixed  $data The input-data
     * @param mixed  $old  The old data
     * @param object $request_data the whole request data object
     * @param string $type The type of the table-column (eg. text, int, varchar, etc)
     *
     * @return mixed The processed data
     */
    public function browse($data, $old, $request_data, $type = null)
    {
        return [
            $this->field => $data,
        ];
    }

    /**
     * Transform data to be shown when editing.
     *
     * @param mixed  $data The input-data
     * @param mixed  $old  The old data
     * @param object $request_data the whole request data object
     * @param string $type The type of the table-column (eg. text, int, varchar, etc)
     *
     * @return mixed The processed data
     */
    public function edit($data, $old, $request_data, $type = null)
    {
        return [
            $this->field => $data,
        ];
    }

    /**
     * Transform data to be stored in the database after updating.
     *
     * @param mixed  $data The input-data
     * @param mixed  $old  The old data
     * @param object $request_data the whole request data object
     * @param string $type The type of the table-column (eg. text, int, varchar, etc)
     *
     * @return mixed The processed data
     */
    public function update($data, $old, $request_data, $type = null)
    {
        return [
            $this->field => $data,
        ];
    }

    /**
     * Transform data to be stored in the database after adding.
     *
     * @param mixed  $data The input-data
     * @param mixed  $old  The old data (null)
     * @param object $request_data the whole request data object
     * @param string $type The type of the table-column (eg. text, integer, etc)
     *
     * @return mixed The processed data
     */
    public function store($data, $old, $request_data, $type = null)
    {
        return [
            $this->field => $data,
        ];
    }

    /**
     * Query a formfield.
     *
     * @param Illuminate\Database\Eloquent\Builder  $query The query-builder
     * @param string                                $field The field-name
     * @param mixed                                 $filter The filter-value
     *
     * @return mixed The processed data
     */
    public function query($query, $field, $filter)
    {
        return $query->where($field, 'like', '%'.$filter.'%');
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
