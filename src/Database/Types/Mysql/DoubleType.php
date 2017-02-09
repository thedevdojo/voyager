<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Types\FloatType as DoctrineFloatType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DoubleType extends DoctrineFloatType
{
    const NAME = 'double';

    public function getName()
    {
        return static::NAME;
    }
}
