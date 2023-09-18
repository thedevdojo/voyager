<?php

namespace TCG\Voyager\Events;

use Illuminate\Queue\SerializesModels;

class TableChanged
{
    use SerializesModels;

    public $name;

    public $changeType;

    public function __construct($name, $changeType)
    {
        $this->name = $name;
        $this->changeType = $changeType;
    }
}
