<?php

namespace TCG\Voyager\Tests;

use Arrilot\Widgets\Facade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use TCG\Voyager\Facades\Voyager;

class WidgetTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();

        Auth::loginUsingId(1);

        Config::set('voyager.dashboard.widgets', [
            'TCG\\Voyager\\Widgets\\UserDimmer',
            'TCG\\Voyager\\Widgets\\PostDimmer',
            'TCG\\Voyager\\Widgets\\PageDimmer',
        ]);
    }

    public function testWidgetsAreRegistered()
    {
        $dimmers = Voyager::dimmers();

        $this->assertEquals(3, $dimmers->count());
    }

    public function testWidgetRenders()
    {
        $dimmers = Voyager::dimmers();

        $this->assertEquals(
            file_get_contents(__DIR__.'/rendered_widgets.html'),
            (string) $dimmers->setSeparator(PHP_EOL.'<hr>'.PHP_EOL)->display()
        );
    }
}
