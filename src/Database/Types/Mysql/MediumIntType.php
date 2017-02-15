<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class MediumIntType extends DoctrineType
{
    const NAME = 'mediumint';

    public function getName()
    {
        return static::NAME;
    }

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        $commonIntegerTypeDeclaration = call_protected_method($platform, '_getCommonIntegerTypeDeclarationSQL', $field);

        return 'mediumint'.$commonIntegerTypeDeclaration;
    }
}
