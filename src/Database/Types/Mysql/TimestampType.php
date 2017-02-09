<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class TimestampType extends DoctrineType
{
    const NAME = 'timestamp';

    public function getName()
    {
        return static::NAME;
    }

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        // if ($column->useCurrent) {
        //     return 'timestamp default CURRENT_TIMESTAMP';
        // }

        return 'timestamp';
    }
}
