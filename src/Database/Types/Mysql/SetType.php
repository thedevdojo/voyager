<?php

namespace TCG\Voyager\Database\Types\Mysql;

use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Illuminate\Support\Facades\DB;

class SetType extends DoctrineType
{
    const NAME = 'set';

    public function getName()
    {
        return static::NAME;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        throw new \Exception('Set type is not supported');

        // we're going to store SET values in the comment since DBAL doesn't support
        $allowed = explode(',', trim($fieldDeclaration['comment']));

        $pdo = DB::connection()->getPdo();

        // trim the values
        $fieldDeclaration['allowed'] = array_map(function($value) use ($pdo) {
            return $pdo->quote(trim($value));
        }, $allowed);

        return "set(".implode(", ", $field['allowed']).")";
    }
}
