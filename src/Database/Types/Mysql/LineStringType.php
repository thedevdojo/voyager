<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use TCG\Voyager\Database\Types\Type;

class LineStringType extends Type
{
    public const NAME = 'linestring';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'linestring';
    }
}
