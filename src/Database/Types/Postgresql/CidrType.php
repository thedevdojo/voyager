<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use TCG\Voyager\Database\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class CidrType extends Type
{
    const NAME = 'cidr';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'cidr';
    }
}
