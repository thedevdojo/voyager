<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class YearType extends DoctrineType
{
    const NAME = 'year';

    public function getName()
    {
        return static::NAME;
    }

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'year';
    }
}
