<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;

class WidgetTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();

        Auth::loginUsingId(1);
    }

    public function testWidgetRenders()
    {
        
    }

    public function testWidgetShowCorrectInformation()
    {
        
    }

    public function testWidgetLinksToTheCorrectPlaces()
    {
        
    }

    public function testRuntimeDisableWidget()
    {
        
    }

    public function testRuntimeEnableWidget()
    {
        
    }

    public function testOverwrittingWidgetView()
    {
        
    }
}
