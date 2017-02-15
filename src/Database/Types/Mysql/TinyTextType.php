<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class TinyTextType extends DoctrineType
{
    const NAME = 'tinytext';

    public function getName()
    {
        return static::NAME;
    }

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'tinytext';
    }
}
