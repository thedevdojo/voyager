<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use TCG\Voyager\Database\Types\Type;

class IntervalType extends Type
{
    const NAME = 'interval';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'interval';
    }
}
