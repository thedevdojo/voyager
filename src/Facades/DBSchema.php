<?php

namespace TCG\Voyager\Facades;

use Exception;
use Illuminate\Support\Facades\DB;
use PDO;

class DBSchema
{
    public static function tables()
    {
        $driver = DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);

        switch ($driver) {
            case 'sqlite':
                $query = "SELECT name FROM sqlite_master WHERE type='table' UNION ALL SELECT name FROM sqlite_temp_master WHERE type='table'";
                break;

            case 'mysql':
                $query = 'SHOW TABLES';
                break;

            case 'pgsql':
                $query = "SELECT table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema = 'public'";
                break;

            default:
                throw new Exception("Voyager: Database driver [$driver] is not supported");
        }

        return DB::select($query);
    }

    /**
     * Describe given table.
     *
     * @param string $table
     *
     * @return \Illuminate\Support\Collection
     */
    public static function describeTable($table)
    {
        $connection = config('database.default', 'mysql');
        $driver = config('database.connections.'.$connection.'.driver', 'mysql');

        if ($driver == 'sqlite') {
            $columns = DB::select(DB::raw("PRAGMA table_info({$table})"));

            return collect($columns)->map(function ($item) {
                return [
                    'field'   => $item->name,
                    'type'    => $item->type,
                    'null'    => ($item->notnull) ? 'NO' : 'YES',
                    'key'     => ($item->pk) ? 'PRI' : '',
                    'default' => ($default = preg_replace("/((^')|('$))/", '', $item->dflt_value)) ? $default : null,
                    'extra'   => ($item->pk == 1 && $item->type == 'integer') ? 'auto_increment' : '',
                ];
            });
        } else {
            $schema_name = DB::connection()->getDatabaseName();
            $raw = "SELECT column_name    AS 'field',
                       column_type    AS 'type',
                       is_nullable    AS 'null',
                       column_key     AS 'key',
                       column_default AS 'default',
                       extra          AS 'extra'
                FROM   information_schema.columns
                WHERE  table_schema = '{$schema_name}'
                AND    table_name = '{$table}'";

            return collect(DB::select(DB::raw($raw)))->map(function ($item) {
                return [
                    'field'   => $item->field,
                    'type'    => $item->type,
                    'null'    => $item->null,
                    'key'     => $item->key,
                    'default' => $item->default,
                    'extra'   => $item->extra,
                ];
            });
        }
    }
}
