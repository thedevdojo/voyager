<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use TCG\Voyager\Database\Types\Type;

class InetType extends Type
{
    public const NAME = 'inet';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'inet';
    }
}
