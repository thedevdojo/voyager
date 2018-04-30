<?php

namespace TCG\Voyager\Tests;

use Exception;
use Illuminate\Foundation\Exceptions\Handler;

class DisabledTestException extends Handler
{
    public function __construct()
    {
    }

    public function report(Exception $e)
    {
    }

    public function render($request, Exception $e)
    {
        throw $e;
    }
}
