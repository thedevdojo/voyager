<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;

class CompassTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    private function enableCompass()
    {
        $this->app['config']->set('voyager.compass_in_production', true);
    }

    private function logString($string)
    {
        \Illuminate\Support\Facades\Log::info($string);
    }

    public function testCantAccessCompass()
    {
        // Can't access compass because environment is != local (testing)
        $response = $this->call('GET', route('voyager.compass.index'));
        $this->assertEquals(403, $response->status());
    }

    public function testCanAccessCompass()
    {
        // Can access compass because we set voyager.compass_in_production configuration to true
        $this->enableCompass();

        $response = $this->call('GET', route('voyager.compass.index'));
        $this->assertEquals(200, $response->status());
    }

    public function testCanSeeLaravelLog()
    {
        $info = 'This is a test log';
        $this->logString($info);
        $this->enableCompass();

        $this->visit(route('voyager.compass.index').'?log='.base64_encode('laravel.log'))
             ->see($info);
    }

    public function testCanDownloadLaravelLog()
    {
        $info = 'This is a downloadable log';
        $this->logString($info);
        $this->enableCompass();

        $response = $this->call('GET', route('voyager.compass.index').'?download='.base64_encode('laravel.log'));
        $response->assertHeader('content-type', 'text/plain');
    }

    public function testCanExecuteCommand()
    {
        $this->enableCompass();

        $response = $this->post(route('voyager.compass.index'), [
            'command' => 'make:model',
            'args' => 'TestModel'
        ]);

        $response->response->assertSee('Model created successfully.');
    }

    public function testCannotExecuteUnknownCommand()
    {
        $this->enableCompass();

        $response = $this->post(route('voyager.compass.index'), [
            'command' => 'unknown:command',
            'args' => 'AnArgument'
        ]);

        $response->response->assertSee('The command "unknown:command" does not exist.');    
    }

    public function testCanDeleteLaravelLog()
    {
        $this->enableCompass();

        $response = $this->call('GET', route('voyager.compass.index').'?del='.base64_encode('laravel.log'));
        $this->assertEquals(302, $response->status()); // Redirect
    }
}
