<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use TCG\Voyager\Database\Types\Type;

class TimeTzType extends Type
{
    const NAME = 'timetz';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'time(0) with time zone';
    }
}
