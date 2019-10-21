<?php

namespace TCG\Voyager\Formfields;

use Illuminate\Support\Carbon;

class DateTime extends BaseFormfield
{
    public $type = 'date-time';

    public function __construct()
    {
        $this->name = __('voyager::bread.formfield.date_time');
        $this->options['type'] = 'datetime';
        $this->options['mode'] = 'all';
        $this->options['range'] = false;
        $this->options['field_second'] = '';
        $this->options['delimiter'] = '-';
    }

    public function browse($data, $model)
    {
        return $this->edit($data, $model);
    }

    public function edit($data, $model)
    {
        if ($this->options['range']) {
            $start = Carbon::parse($model->{$this->field})->locale('de');
            $end = Carbon::parse($model->{$this->options['field_second']})->locale('de');
            $type = $this->options['type'];
            if ($type == 'date') {
                $start = $start->format('Y-m-d');
                $end = $end->format('Y-m-d');
            } elseif ($type == 'time') {
                $start = $start->format('H:i:s');
                $end = $end->format('H:i:s');
            } elseif ($type == 'month') {
                $start = $start->format('M YYYY');
                $end = $end->format('M YYYY');
            } else {
                $start = $start->format('Y-m-d H:i:s');
                $end = $end->format('Y-m-d H:i:s');
            }
            return [
                $this->field => [
                    'start' => $start,
                    'end'   => $end,
                ],
            ];
        }

        return [
            $this->field => $data,
        ];
    }

    public function update($data, $old, $model, $request_data)
    {
        if ($this->options['range']) {
            $field = $this->field;
            $field_2 = $this->options['field_second'];

            return [
                $field   => $data->start ?? '',
                $field_2 => $data->end ?? '',
            ];
        }

        return [
            $this->field => $data,
        ];
    }

    public function store($data, $old, $model, $request_data)
    {
        return $this->update($data, $old, $model, $request_data);
    }

    public function query($query, $field, $filter)
    {
        if ($this->options['range']) {
            // TODO: Query if range in in filter-range
        }

        return $query->whereBetween($field, [$filter['start'], $filter['end']]);
    }
}
