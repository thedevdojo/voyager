<?php

namespace TCG\Voyager\Interfaces;

interface User {
    public function role();

    public function hasRole($name);

    public function setRole($name);

    public function hasPermission($name);

    public function hasPermissionOrFail($name);

    public function hasPermissionOrAbort($name, $statusCode = 403);
}
