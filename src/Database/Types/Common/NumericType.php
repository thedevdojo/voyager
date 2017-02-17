<?php

namespace TCG\Voyager\Database\Types\Common;

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
