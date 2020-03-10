<?php

namespace TCG\Voyager\Plugins\Interfaces;

interface AuthorizationInterface extends BaseInterface
{
    public function authorize($ability, $arguments = []);
}
