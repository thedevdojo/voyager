<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use TCG\Voyager\Database\Types\Type;

class TimestampType extends Type
{
    const NAME = 'timestamp';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'timestamp';
    }
}
