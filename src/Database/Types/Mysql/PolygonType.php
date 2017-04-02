<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use TCG\Voyager\Database\Types\Type;

class PolygonType extends Type
{
    const NAME = 'polygon';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'polygon';
    }
}
