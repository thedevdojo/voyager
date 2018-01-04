<?php

declare(strict_types=1);

namespace TCG\Voyager\Traits;

trait RelationshipCache
{
    protected static $relationships = [];

    public static function getRelationship($id)
    {
        if (! isset(self::$relationships[$id])) {
            self::$relationships[$id] = self::find($id);
        }

        return self::$relationships[$id];
    }
}
