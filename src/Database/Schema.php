<?php

namespace TCG\Voyager\Database;

use Doctrine\DBAL\Schema\SchemaException;
use Illuminate\Support\Facades\DB;

class Schema
{
    public static function manager()
    {
        return DB::connection()->getDoctrineSchemaManager();
    }

    public static function tableExists($table)
    {
        return static::manager()->tablesExist([$table]);
    }

    public static function getDoctrineTable($table)
    {
        if (!static::tableExists($table)) {
            throw SchemaException::tableDoesNotExist($table);
        }

        return static::manager()->listTableDetails($table);
    }

    public static function getDoctrineColumn($table, $column)
    {
        return static::getDoctrineTable($table)->getColumn($column);
    }

    public static function tables()
    {
        return static::manager()->listTableNames();
    }
}
