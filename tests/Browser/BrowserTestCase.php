<?php

namespace TCG\Voyager\Tests\Browser;

use Orchestra\Testbench\Dusk\TestCase;

class BrowserTestCase extends TestCase
{
    public static function setUpBeforeClass()
    {
        static::serve();
    }

    public static function tearDownAfterClass()
    {
        static::stopServing();
    }
}
