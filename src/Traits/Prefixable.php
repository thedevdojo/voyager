<?php

namespace TCG\Voyager\Traits;

use Illuminate\Support\Str;

trait Prefixable
{
    protected $use_table_prefix = true;

    public function __construct(array $attributes = [])
    {
        if ($this->use_table_prefix && !empty(config('voyager.database.table_prefix'))) {
            // $table = strtolower(Str::plural(Str::snake(class_basename(get_class($this)))));
            $this->setTable(config('voyager.database.table_prefix').$this->getTable());
        }

        parent::__construct($attributes);
    }
}
