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

        return array_map(function ($table) {
            $table = get_object_vars($table);

            return reset($table);
        }, DB::select($query));
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

        switch ($driver) {
            case 'mysql':
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

            case 'pgsql':
                $schema_name = DB::connection()->getDatabaseName();
                $raw = "SELECT  
                        f.attnum AS number,  
                        f.attname AS name,  
                        f.attnum,  
                        f.attnotnull AS notnull,  
                        pg_catalog.format_type(f.atttypid,f.atttypmod) AS type,  
                        CASE  
                            WHEN p.contype = 'p' THEN 't'  
                            ELSE 'f'  
                        END AS primarykey,  
                        CASE  
                            WHEN p.contype = 'u' THEN 't'  
                            ELSE 'f'
                        END AS uniquekey,
                        CASE
                            WHEN p.contype = 'f' THEN g.relname
                        END AS foreignkey,
                        CASE
                            WHEN p.contype = 'f' THEN p.confkey
                        END AS foreignkey_fieldnum,
                        CASE
                            WHEN p.contype = 'f' THEN g.relname
                        END AS foreignkey,
                        CASE
                            WHEN p.contype = 'f' THEN p.conkey
                        END AS foreignkey_connnum,
                        CASE
                            WHEN f.atthasdef = 't' THEN d.adsrc
                        END AS default
                    FROM pg_attribute f  
                        JOIN pg_class c ON c.oid = f.attrelid  
                        JOIN pg_type t ON t.oid = f.atttypid  
                        LEFT JOIN pg_attrdef d ON d.adrelid = c.oid AND d.adnum = f.attnum  
                        LEFT JOIN pg_namespace n ON n.oid = c.relnamespace  
                        LEFT JOIN pg_constraint p ON p.conrelid = c.oid AND f.attnum = ANY (p.conkey)  
                        LEFT JOIN pg_class AS g ON p.confrelid = g.oid  
                    WHERE c.relkind = 'r'::char  
                    "/*
                        AND n.nspname = '{$schema_name}'
                        AND c.relname = '{$table}'
                    */.'
                        AND f.attnum > 0 ORDER BY number
                ';

                dd(DB::select(DB::raw($raw)));

            default:
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
        }
    }
}
