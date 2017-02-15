<?php

namespace TCG\Voyager\Database\Types\Common;

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
