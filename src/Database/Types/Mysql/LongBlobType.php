<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class LongBlobType extends DoctrineType
{
    const NAME = 'longblob';

    public function getName()
    {
        return static::NAME;
    }

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'longblob';
    }
}
