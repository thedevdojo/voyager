<?php

namespace TCG\Voyager\Database\Schema;

use Illuminate\Support\Facades\Validator;

abstract class Identifier
{
    // Warning: Do not modify this
    const REGEX = '^[a-zA-Z_][a-zA-Z0-9_]*$';

    public static function validate($identifier, $asset = '')
    {
        $identifier = trim($identifier);

        $validator = Validator::make(['identifier' => $identifier], [
            'identifier' => 'required|regex:'.'/'.static::REGEX.'/',
        ]);

        if ($validator->fails()) {
            throw new \Exception("{$asset} Identifier {$identifier} is invalid");
        }

        return $identifier;
    }
}
