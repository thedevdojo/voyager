<?php

namespace TCG\Voyager\Plugins\Interfaces;

interface AuthorizationInterface extends GenericInterface
{
    public function authorize($ability, $arguments = []);
}
