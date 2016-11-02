<?php

namespace TCG\Voyager\Facades;

use DB;
use PDO;

class DBSchema 
{

    const SQLITE = 'sqlite';
    const MYSQL = 'mysql';
    const PGSQL = 'pgsql';

    public static function tables() 
    {
        if (static::isSQLITE()) {
            return DB::select("SELECT name FROM sqlite_master WHERE type='table' UNION ALL SELECT name FROM sqlite_temp_master WHERE type='table'");
        }

        if (static::isMYSQL()) {
            return DB::select("SHOW TABLES");
        }

        if (static::isPOSTGRES()) {
            return DB::select("SELECT table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema = 'public'");
        }

        throw new \Exception("Voyager: Database driver not supported");
    }

    public static function isSQLITE() 
    {
        return DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME) == self::SQLITE;
    }

    public static function isMYSQL() 
    {
        return DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME) == self::MYSQL;
    }

    public static function isPOSTGRES() 
    {
        return DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME) == self::PGSQL;
    }

}