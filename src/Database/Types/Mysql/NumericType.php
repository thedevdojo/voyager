<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Types\DecimalType as DoctrineDecimalType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class NumericType extends DoctrineDecimalType
{
    const NAME = 'numeric';

    public function getName()
    {
        return static::NAME;
    }
}
