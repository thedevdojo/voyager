<?php

namespace TCG\Voyager\Database\Platforms;

use Illuminate\Support\Collection;
use TCG\Voyager\Database\Types\Type;

abstract class Oracle extends Platform
{
    public static function getTypes(Collection $typeMapping)
    {
        return $typeMapping;
    }

    public static function registerCustomTypeOptions()
    {

    }
}
