<?php

namespace TCG\Voyager\Formfields;

class DateTime extends BaseFormfield
{
    public $type = 'date-time';

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield_date_time');
        $this->options['type'] = 'datetime';
        $this->options['mode'] = 'all';
        $this->options['format'] = 'd-m-Y H:i';
        $this->options['range'] = '';
    }

    public function update($data, $old, $type = null)
    {
        // date, datetime, time
        if ($type == 'date') {
        } elseif ($type == 'datetime') {
        } elseif ($type == 'time') {
        }

        return $data;
    }
}
