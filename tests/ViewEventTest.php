<?php

namespace TCG\Voyager\Tests;

use TCG\Voyager\Facades\Voyager;

class ViewEventTest extends TestCase
{
    public $eventTrigered = false;

    public function setUp()
    {
        parent::setUp();

        $this->install();

        $this->disableExceptionHandling();

        // Add test view
        app('view')->addLocation(__DIR__.'/views');

        // Add test route
        $this->app['router']->get('test', function () {
            return Voyager::view('test', [
                'foo' => 'bar',
            ]);
        });
    }

    public function testRenderingViewTriggersEvent()
    {
        // Load view, and test if view works
        $this->get('test')
            ->see('This is a test');

        // Add event on test view
        Voyager::onLoadingView('test', function ($name, array $parameters) {
            $this->eventTrigered = true;

            $this->assertEquals('test', $name);
            $this->assertArrayHasKey('foo', $parameters);
            $this->assertEquals('bar', $parameters['foo']);
        });

        // Load view to trigger the event
        $this->get('test');

        // Test if event is triggered
        $this->assertTrue($this->eventTrigered);
    }

    public function testOverwritingViewName()
    {
        // Add event on test view
        Voyager::onLoadingView('test', function (&$name, array $parameters) {
            $name = 'foo';
        });

        // Load view to trigger the event, and see if the new view is used
        $this->get('test')
            ->see('This is the foo view');
    }

    public function testOverwritingViewNameAndParameters()
    {
        // Add event on test view
        Voyager::onLoadingView('test', function (&$name, array &$parameters) {
            $name = 'hello';
            $parameters['name'] = 'Mark';
        });

        // Load view to trigger the event, and see if the new view is used
        $this->get('test')
            ->see('Hello Mark!');
    }
}
