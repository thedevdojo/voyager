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

    public function tearDown(): void
    {
        parent::tearDown();

        if (file_exists(base_path('app/Models/TestModel.php'))) {
            unlink(base_path('app/Models/TestModel.php'));
        }
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

    public function testCanExecuteCommand()
    {
        $this->enableCompass();

        $response = $this->post(route('voyager.compass.index'), [
            'command' => 'make:model',
            'args'    => 'TestModel',
        ]);
        $this->assertStringContainsString('created successfully.', $response->response->content());
    }

    public function testCannotExecuteUnknownCommand()
    {
        $this->enableCompass();

        $response = $this->post(route('voyager.compass.index'), [
            'command' => 'unknown:command',
            'args'    => 'AnArgument',
        ]);
        $this->assertStringContainsString('The command &quot;unknown:command&quot; does not exist.', $response->response->content());
    }

    public function testCanDeleteLaravelLog()
    {
        $this->enableCompass();

        $response = $this->call('GET', route('voyager.compass.index').'?del='.base64_encode('laravel.log'));
        $this->assertEquals(302, $response->status()); // Redirect
    }
}
