<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use TCG\Voyager\Database\Types\Type;

class SmallIntType extends Type
{
    const NAME = 'smallint';
    const DBTYPE = 'int2';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        $commonIntegerTypeDeclaration = call_protected_method($platform, '_getCommonIntegerTypeDeclarationSQL', $field);

        $type = $field['autoincrement'] ? 'smallserial' : 'smallint';

        return $type.$commonIntegerTypeDeclaration;
    }
}
