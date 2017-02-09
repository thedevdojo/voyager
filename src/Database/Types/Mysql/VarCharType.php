<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Types\StringType as DoctrineStringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class VarCharType extends DoctrineStringType
{
    const NAME = 'varchar';

    public function getName()
    {
        return static::NAME;
    }
}
