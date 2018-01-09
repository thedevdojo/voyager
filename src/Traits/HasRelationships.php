<?php

namespace TCG\Voyager\Traits;

trait HasRelationships
{
    protected static $relationships = [];

    public static function getRelationship($id)
    {
        if (!isset(self::$relationships[$id])) {
            self::$relationships[$id] = self::find($id);
        }

        return self::$relationships[$id];
    }
}
