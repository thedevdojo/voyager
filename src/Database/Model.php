<?php

namespace TCG\Voyager\Database;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
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
