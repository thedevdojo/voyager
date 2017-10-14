<?php

namespace TCG\Voyager\Events;

use Illuminate\Queue\SerializesModels;

class AlertsCollection
{
    use SerializesModels;

    public $collection;

    public function __construct(array $collection)
    {
        $this->collection = $collection;

        // Deprecate on v1.3
        //
        event('voyager.alerts.collecting', $collection);
    }
}
