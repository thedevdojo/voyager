<?php

namespace TCG\Voyager\FormFields;

class CoordinatesHandler extends AbstractHandler
{
    protected $supports = [
        'mysql',
        'pgsql',
    ];

    protected $codename = 'coordinates';
}
