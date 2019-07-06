<?php

include __DIR__.'/../vendor/autoload.php';

use Carbon\Carbon;

date_default_timezone_set('UTC');
Carbon::setTestNow(Carbon::now());

Orchestra\Testbench\Dusk\Options::withoutUI();
