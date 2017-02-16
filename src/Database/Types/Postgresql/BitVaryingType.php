<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use TCG\Voyager\Database\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class BitVaryingType extends Type
{
    const NAME = 'bit varying';
    const DBTYPE = 'varbit';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        $length = empty($field['length']) ? 255 : $field['length'];

        return "varbit({$length})";
    }
}
