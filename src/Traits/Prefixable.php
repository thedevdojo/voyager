<?php

namespace TCG\Voyager\Traits;

trait Prefixable
{
    protected $use_table_prefix = true;

    public function __construct(array $attributes = [])
    {
        if ($this->use_table_prefix && !empty(config('voyager.database.table_prefix'))) {
            $this->setTable(config('voyager.database.table_prefix').$this->getTable());
        }

        parent::__construct($attributes);
    }
}
