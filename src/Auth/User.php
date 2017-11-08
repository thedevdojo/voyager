<?php

namespace TCG\Voyager\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public function __construct()
    {
        $voyagerDBConn = config('voyager.database.connection');

        if ($voyagerDBConn) {
            $this->connection = $voyagerDBConn;
        }

        parent::__construct(...func_get_args());
    }
}
