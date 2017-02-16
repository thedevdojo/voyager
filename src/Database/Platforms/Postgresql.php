<?php

namespace TCG\Voyager\Database\Platforms;

use TCG\Voyager\Database\Types\Type;
use Illuminate\Support\Collection;

abstract class Postgresql extends Platform
{
    public static function getTypes(Collection $typeMapping)
    {
        // todo: need to create
            // box, circle, line, lseg, path, pg_lsn, point, polygon
        // todo: add a Geometry category (also add Mysql types)

        $typeMapping->forget([
            'smallint',
            'serial',
            'serial4',
            'int',
            'integer',
            'bigserial',
            'serial8',
            'bigint',
            'decimal',
            'float',
            'real',
            'double',
            'double precision',
            'boolean',
            '_varchar',
            'char',
            'datetime',
            'year',
        ]);

        return $typeMapping;
    }

    public static function registerCustomTypeOptions()
    {

    }
}
