<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use TCG\Voyager\Database\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class XmlType extends Type
{
    const NAME = 'xml';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'xml';
    }
}
