<?php

namespace TCG\Voyager\Database\Platforms;

use TCG\Voyager\Database\Types\Type;
use Illuminate\Support\Collection;

abstract class Mysql extends Platform
{
    public static function getTypes(Collection $typeMapping)
    {
        $typeMapping->forget([
            'real',    // same as double
            'int',     // same as integer
            'string',  // same as varchar
            'numeric', // same as decimal
        ]);

        return $typeMapping;
    }

    public static function registerCustomTypeOptions()
    {
        Type::registerCustomOption(Type::NOT_SUPPORTED, true, [
            'enum',
            'set',
        ]);

        Type::registerCustomOption(Type::NOT_SUPPORT_INDEX, true, '*text');
        Type::registerCustomOption(Type::NOT_SUPPORT_INDEX, true, '*blob');
    }
}
