<?php

namespace TCG\Voyager\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Routing\Router;

class Routing
{
    use SerializesModels;

    public $router;

    public function __construct(Router $router)
    {
        $this->router = $router;

        // Deprecate on v1.3
        //
        event('voyager.routing', $router);
    }
}
