<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Exceptions\Handler;

class DisabledTestException extends Handler
{
    public function __construct()
    {
    }

    public function report($e)
    {
    }

    public function render($request, $e)
    {
        throw $e;
    }
}
