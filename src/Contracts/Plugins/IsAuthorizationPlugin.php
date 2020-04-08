<?php

namespace TCG\Voyager\Contracts\Plugins;

interface IsAuthorizationPlugins extends IsGenericPlugin
{
    public function authorize($ability, $arguments = []);
}
