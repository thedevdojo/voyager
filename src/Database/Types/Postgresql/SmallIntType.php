<?php

namespace TCG\Voyager\Database\Types\Postgresql;

use TCG\Voyager\Database\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class SmallIntType extends Type
{
    const NAME = 'smallint';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        $commonIntegerTypeDeclaration = call_protected_method($platform, '_getCommonIntegerTypeDeclarationSQL', $field);

        $type = $field['autoincrement'] ? 'smallserial' : 'smallint';

        return $type.$commonIntegerTypeDeclaration;
    }
}
