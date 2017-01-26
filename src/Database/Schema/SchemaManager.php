<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Schema\Index as DoctrineIndex;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\DB;

class SchemaManager
{
    public static function manager()
    {
        return DB::connection()->getDoctrineSchemaManager();
    }

    public static function alterTable(TableDiff $tableDiff)
    {
        static::manager()->alterTable($tableDiff);
    }

    public static function tableExists($table)
    {
        if (!is_array($table)) {
            $table = [$table];
        }

        return static::manager()->tablesExist($table);
    }

    public static function getTable($table)
    {
        return new Table(static::getDoctrineTable($table));
    }

    public static function getDoctrineTable($table)
    {
        if (!static::tableExists($table)) {
            throw SchemaException::tableDoesNotExist($table);
        }

        return static::manager()->listTableDetails($table);
    }

    public static function getColumn($table, $column)
    {
        return new Column(static::getDoctrineColumn($table, $column));
    }

    public static function getDoctrineColumn($table, $column)
    {
        return static::getDoctrineTable($table)->getColumn($column);
    }

    public static function getDoctrineColumnFromArray(array $column)
    {
        $name = $column['name'];
        $type = $column['type'];
        $type = ($type instanceof Type) ? $type : Type::getType($type);
        $options = array_diff_key($column, ['name' => $name, 'type' => $type]);

        return new DoctrineColumn($name, $type, $options);
    }

    public static function getDoctrineIndexFromArray(array $index)
    {
        $type = Index::validateType($index['type']);

        $columns = $index['columns'];
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $isPrimary = ($type == Index::PRIMARY);
        $isUnique = $isPrimary || ($type == Index::UNIQUE);

        // Set the name
        if (isset($index['name'])) {
            $name = $index['name'];
        } else {
            $table = isset($index['table']) ? $index['table'] : null;
            $name = Index::createName($columns, $type, $table);
        }

        $flags = isset($index['flags']) ? $index['flags'] : [];
        $options = isset($index['options']) ? $index['options'] : [];
        
        return new DoctrineIndex($name, $columns, $isUnique, $isPrimary, $flags, $options);
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
