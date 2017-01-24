<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\SchemaException;
use Illuminate\Support\Facades\DB;

class SchemaManager
{
    // TODO: Change these methods to return the custom Table and Column instances

    public static function manager()
    {
        return DB::connection()->getDoctrineSchemaManager();
    }

    public static function tableExists($table)
    {
        if (!is_array($table)) {
            $table = [$table];
        }

        return static::manager()->tablesExist($table);
    }

    public static function listTableDetails($table)
    {
        return static::manager()->listTableDetails($table);
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

    public static function listTables()
    {
        return static::manager()->listTables();
    }

    public static function listTableNames()
    {
        return static::manager()->listTableNames();
    }
}
