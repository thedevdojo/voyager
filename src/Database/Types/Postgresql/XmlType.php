<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use TCG\Voyager\Database\Types\Type;

class XmlType extends Type
{
    public const NAME = 'xml';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'xml';
    }
}
