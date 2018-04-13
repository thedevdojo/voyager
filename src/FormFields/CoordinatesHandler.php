<?php

namespace TCG\Voyager\FormFields;

class CoordinatesHandler extends AbstractHandler
{
    protected $supports = [
        'mysql',
        'pgsql',
    ];

    protected $codename = 'coordinates';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        $points = getCoordinates($dataTypeContent);
        $point = isset($points[$row->field]) ? $points[$row->field] : null;

        return view('voyager::formfields.coordinates', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
            'point'           => $point,
        ]);
    }
}
