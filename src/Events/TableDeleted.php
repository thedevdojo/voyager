<?php

namespace TCG\Voyager\Events;

use Illuminate\Queue\SerializesModels;

class TableDeleted
{
    use SerializesModels;

    public $name;

    public function __construct($name)
    {
        $this->name = $name;

        event(new TableChanged($name, 'Deleted'));
    }
}
