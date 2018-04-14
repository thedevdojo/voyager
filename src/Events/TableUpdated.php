<?php

namespace TCG\Voyager\Events;

use Illuminate\Queue\SerializesModels;

class TableUpdated
{
    use SerializesModels;

    public $name;

    public $originalTable;

    public function __construct(array $name, array $originalTable)
    {
        $this->name = $name;
        $this->originalTable = $originalTable;

        event(new TableChanged($name['name'], 'Updated'));
    }
}
