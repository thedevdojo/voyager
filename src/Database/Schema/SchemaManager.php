<?php

namespace TCG\Voyager\Database\Schema;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;

abstract class SchemaManager
{
    public static function __callStatic($method, $args)
    {
        return static::manager()->$method(...$args);
    }

    public static function manager()
    {
        return DB::connection();
    }

    public static function getDatabaseConnection()
    {
        return DB::connection();
    }

    public static function tableExists($table)
    {
        if (!is_array($table)) {
            $table = [$table];
        }

        return Schema::hasTable($table[0]);
    }

    public static function listTables()
    {
        $tables = [];
        $tableNames = Schema::getConnection()->getSchemaBuilder()->getTables();

        foreach ($tableNames as $tableName) {
            $tables[$tableName] = static::listTableDetails($tableName);
        }

        return $tables;
    }

    public static function listTableDetails($tableName)
    {
        $columns = Schema::getColumnListing($tableName);
        $columnDetails = collect($columns)->mapWithKeys(function ($column) use ($tableName) {
            return [$column => static::getColumnDetails($tableName, $column)];
        });

        $indexes = static::getTableIndexes($tableName);
        $foreignKeys = static::getTableForeignKeys($tableName);

        return new Table($tableName, $columnDetails->toArray(), $indexes, [], $foreignKeys, []);
    }

    public static function describeTable($tableName)
    {
        $columns = Schema::getColumnListing($tableName);

        return collect($columns)->map(function ($column) use ($tableName) {
            $columnDetails = static::getColumnDetails($tableName, $column);
            $indexes = static::getColumnIndexes($tableName, $column);

            if (!empty($indexes) && isset($indexes[1])) {
                $indexes = [$indexes[1]];
            }

            return [
                'field' => $column,
                'type' => $columnDetails['type'],
                'null' => $columnDetails['nullable'],
                'key' => !empty($indexes) ? substr($indexes[0]['type'], 0, 3) : null,
                'default' => $columnDetails['default'],
                'extra' => $columnDetails['auto_increment'] ? 'auto_increment' : '',
                'indexes' => $indexes,
            ];
        });
    }

    public static function listTableColumnNames($tableName)
    {
        return Schema::getColumnListing($tableName);
    }

    public static function createTable($table)
    {
        if ($table instanceof Blueprint) {
            Schema::create($table->getTable(), function (Blueprint $blueprint) use ($table) {
                foreach ($table->getColumns() as $column) {
                    $blueprint->addColumn(
                        $column->getType()->getName(),
                        $column->getName(),
                        $column->toArray()
                    );
                }
            });
        } else {
            throw new \InvalidArgumentException('Table must be an instance of Blueprint');
        }
    }

    protected static function getColumnDetails($table, $column)
    {
        $schema = Schema::getConnection()->getSchemaBuilder();
        $columnType = $schema->getColumnType($table, $column);
        $columnDefinition = $schema->getColumns($table);

        $columnInfo = collect($columnDefinition)->firstWhere('name', $column);

        if (!$columnInfo) {
            throw new \InvalidArgumentException("Column '$column' not found in table '$table'.");
        }

        return [
            'type' => $columnType,
            'nullable' => !($columnInfo['nullable'] ?? false),
            'default' => $columnInfo['default'] ?? null,
            'auto_increment' => ($columnInfo['auto_increment'] ?? false),
        ];
    }

    protected static function getTableIndexes($table)
    {
        return DB::getSchemaBuilder()->getIndexes($table);
    }

    protected static function getColumnIndexes($table, $column)
    {
        $tableIndexes = static::getTableIndexes($table);
        return collect($tableIndexes)->filter(function ($index) use ($column) {
            return in_array($column, $index['columns']);
        })->toArray();
    }

    protected static function getTableForeignKeys($table)
    {
        return DB::getSchemaBuilder()->getForeignKeys($table);
    }

    public static function listTableNames()
    {
        $connection = Schema::getConnection();

        // Check if the connection supports the getTables method
        if (method_exists($connection->getSchemaBuilder(), 'getTables')) {
            $tables = $connection->getSchemaBuilder()->getTables();
            return collect($tables)->pluck('name')->values()->all();
        }

        // Fallback method if getTables is not available
        $tables = $connection->getDoctrineSchemaManager()->listTableNames();

        // Filter out tables that should be excluded (like migrations)
        $excludedTables = ['migrations', 'failed_jobs', 'password_resets'];
        return array_values(array_diff($tables, $excludedTables));
    }
}
