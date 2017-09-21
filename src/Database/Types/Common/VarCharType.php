<?php

namespace TCG\Voyager\Database\Types\Common;

use Doctrine\DBAL\Types\StringType as DoctrineStringType;

class VarCharType extends DoctrineStringType
{
    const NAME = 'varchar';

    public function getName()
    {
        return static::NAME;
    }
}
