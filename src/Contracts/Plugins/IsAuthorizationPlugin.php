<?php

namespace TCG\Voyager\Contracts\Plugins;

interface IsAuthorizationPlugin extends IsGenericPlugin
{
    public function authorize($ability, $arguments = []);
}
