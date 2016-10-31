<?php

namespace TCG\Voyager\Facades;

use DB;
use PDO;

class DBSchema 
{

    const SQLITE = 'sqlite';

    public static function tables() 
    {
        if (static::isSQLITE()) {
            return DB::select("SELECT name FROM sqlite_master WHERE type='table' UNION ALL SELECT name FROM sqlite_temp_master WHERE type='table'");
        }

        return DB::select('SHOW TABLES');
    }

    public static function isSQLITE() 
    {
        return DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME) == self::SQLITE;
    }

}