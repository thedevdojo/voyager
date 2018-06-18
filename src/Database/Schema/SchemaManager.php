<?php

namespace TCG\Voyager\Database\Schema;

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table as DoctrineTable;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Types\Type;

abstract class SchemaManager
{
    // todo: trim parameters

    public static function __callStatic($method, $args)
    {
        return static::manager()->$method(...$args);
    }

    public static function manager($connection = null)
    {
        return DB::connection($connection)->getDoctrineSchemaManager();
    }

    public static function getDatabaseConnection($connection = null)
    {
        return DB::connection($connection)->getDoctrineConnection();
    }

    public static function tableExists($table, $connection = null)
    {
        if (!is_array($table)) {
            $table = [$table];
        }

        return static::manager($connection)->tablesExist($table);
    }

    public static function listTables($connection = null)
    {
        $tables = [];

        foreach (static::manager($connection)->listTableNames() as $tableName) {
            $tables[$tableName] = static::listTableDetails($tableName);
        }

        return $tables;
    }

    /**
     * @param string $tableName
     *
     * @return \TCG\Voyager\Database\Schema\Table
     */
    public static function listTableDetails($tableName, $connection = null)
    {
        $columns = static::manager($connection)->listTableColumns($tableName);

        $foreignKeys = [];
        if (static::manager($connection)->getDatabasePlatform()->supportsForeignKeyConstraints()) {
            $foreignKeys = static::manager($connection)->listTableForeignKeys($tableName);
        }

        $indexes = static::manager($connection)->listTableIndexes($tableName);

        return new Table($tableName, $columns, $indexes, $foreignKeys, false, []);
    }

    /**
     * Describes given table.
     *
     * @param string $tableName
     *
     * @return \Illuminate\Support\Collection
     */
    public static function describeTable($tableName, $connection = null)
    {
        Type::registerCustomPlatformTypes();

        $table = static::listTableDetails($tableName, $connection);

        return collect($table->columns)->map(function ($column) use ($table) {
            $columnArr = Column::toArray($column);

            $columnArr['field'] = $columnArr['name'];
            $columnArr['type'] = $columnArr['type']['name'];

            // Set the indexes and key
            $columnArr['indexes'] = [];
            $columnArr['key'] = null;
            if ($columnArr['indexes'] = $table->getColumnsIndexes($columnArr['name'], true)) {
                // Convert indexes to Array
                foreach ($columnArr['indexes'] as $name => $index) {
                    $columnArr['indexes'][$name] = Index::toArray($index);
                }

                // If there are multiple indexes for the column
                // the Key will be one with highest priority
                $indexType = array_values($columnArr['indexes'])[0]['type'];
                $columnArr['key'] = substr($indexType, 0, 3);
            }

            return $columnArr;
        });
    }

    public static function listTableColumnNames($tableName, $connection = null)
    {
        Type::registerCustomPlatformTypes();

        $columnNames = [];

        foreach (static::manager($connection)->listTableColumns($tableName) as $column) {
            $columnNames[] = $column->getName();
        }

        return $columnNames;
    }

    public static function createTable($table, $connection = null)
    {
        if (!($table instanceof DoctrineTable)) {
            $table = Table::make($table);
        }

        static::manager($connection)->createTable($table);
    }

    public static function getDoctrineTable($table, $connection = null)
    {
        $table = trim($table);

        if (!static::tableExists($table)) {
            throw SchemaException::tableDoesNotExist($table);
        }

        return static::manager($connection)->listTableDetails($table);
    }

    public static function getDoctrineColumn($table, $column)
    {
        return static::getDoctrineTable($table)->getColumn($column);
    }

    public static function getConnectionNameByTable($table)
    {
        $remoteDatabaseConnections = config('voyager.remote_databases_connections') ?? null;

        if ($remoteDatabaseConnections) {
            foreach ($remoteDatabaseConnections as $conn) {
                if (static::tableExists($table, $conn))
                {
                    return $connection = $conn;
                }
            }
        }

        return null;
    }

    public static function getRemoteDatabasesTableNames()
    {
        $remoteDatabaseConnections = config('voyager.remote_databases_connections') ?? null;
        $remoteDatabasesTables = [];

        if ($remoteDatabaseConnections) {
            foreach ($remoteDatabaseConnections as $conn) {
                $remoteDatabasesTables = array_merge($remoteDatabasesTables, static::manager($conn)->listTableNames());
            }
        }

        return $remoteDatabasesTables;
    }
}
