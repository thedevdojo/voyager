<?php

namespace TCG\Voyager\Events;

use Illuminate\Routing\Router;
use Illuminate\Queue\SerializesModels;

class RoutingAdmin
{
    use SerializesModels;

    public $router;

    public function __construct(Router $router)
    {
        $this->router = $router;

        // Deprecate on v1.3
        //
        event('voyager.admin.routing', $router);
    }
}
