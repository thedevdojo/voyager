<?php

namespace TCG\Voyager\Tests;

use Arrilot\Widgets\Facade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\User;

class WidgetTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();

        $this->withFactories(__DIR__.'/database/factories');

        Auth::loginUsingId(1);
    }

    public function testVoyagerFacadeReturnsDimmers()
    {
        $dimmers = Voyager::dimmers();

        $this->assertEquals(3, $dimmers->count());
    }

    public function testVoyagerFacadeReturnsOnlyDimmersThatAreAccessible()
    {
        // Since this user contains a random role without any permissions, we
        // can use it to check if the widgets are being added to the group.
        $user = factory(User::class)->create();
        Auth::loginUsingId($user->id);

        $dimmers = Voyager::dimmers();

        $this->assertEquals(0, $dimmers->count());
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
