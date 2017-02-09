<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Illuminate\Support\Facades\DB;

class EnumType extends DoctrineType
{
    const NAME = 'enum';

    public function getName()
    {
        return static::NAME;
    }

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        throw new \Exception('Enum type is not supported');
        // get allowed from $column instance???
        // learn more about this....

        $pdo = DB::connection()->getPdo();

        // trim the values
        $allowed = array_map(function($value) use ($pdo) {
            return $pdo->quote(trim($value));
        }, $allowed);

        return "enum(".implode(", ", $allowed).")";
    }
}
