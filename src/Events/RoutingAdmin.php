<?php

namespace TCG\Voyager\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Routing\Router;

class RoutingAdmin
{
    use SerializesModels;

    public $router;

    public function __construct(Router $router)
    {
        $this->router = $router;

        // @deprecate on v1.3
        //
        event('voyager.admin.routing', $router);
    }
}
