<?php

namespace TCG\Voyager\Tests;

use Arrilot\Widgets\Facade;
use Illuminate\Support\Facades\Auth;

class WidgetTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();

        Auth::loginUsingId(1);
    }

    public function testWidgetsAreRegistered()
    {
        $dimmers = Facade::group('voyager::dimmers');

        $this->assertEquals(3, $dimmers->count());
    }

    public function testWidgetRenders()
    {
        $dimmers = Facade::group('voyager::dimmers');

        $this->assertEquals(
            file_get_contents(__DIR__.'/rendered_widgets.html'),
            (string) $dimmers->setSeparator("\n<hr>\n")->display()
        );
    }
}
