<?php

namespace TCG\Voyager\Database\Platforms;

use TCG\Voyager\Database\Types\Type;
use Illuminate\Support\Collection;

abstract class Postgresql extends Platform
{
    public static function getTypes(Collection $typeMapping)
    {
        return $typeMapping;
    }

    public static function registerCustomTypeOptions()
    {

    }
}
