<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TCG\Voyager\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class CompassTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->install();

        $this->user = Auth::loginUsingId(1);
    }

    public function testCommandWithoutArgument()
    {
        $response = $this->post(route('voyager.compass.post'), [
            'command' => 'clear-compiled',
            'args' => null,
        ])->see('Compiled services and packages files removed!');
    }

    public function testCommandWithArgument()
    {
        $response = $this->post(route('voyager.compass.post'), [
            'command' => 'help',
            'args' => '--version',
        ])->see('Laravel Framework');
    }

    public function testCommandWithCommandInjectionInArgument()
    {
        $response = $this->post(route('voyager.compass.post'), [
            'command' => 'help',
            'args' => '; cat /etc/passwd',
        ])->see('General error');
    }

    public function testCommandWithCommandInjectionInCommand()
    {
        $response = $this->post(route('voyager.compass.post'), [
            'command' => 'clear-compiled ; echo "evil"',
            'args' => null,
        ])->see('General error');
    }

    public function testCommandWithCommandInjectionAsCommandTwo()
    {
        $response = $this->post(route('voyager.compass.post'), [
            'command' => 'clear-compiled && echo "evil"',
            'args' => null,
        ])->see('General error');
    }

    public function testFailDownloadingNonLogFile()
    {
        // Trying to reach a file with an aboslute file path,
        // eg. this try to grab the .env in the base project
        $log = base64_encode(
            realpath(base_path('../../../../../../../.env')));

        $response = $this->get(route('voyager.compass.index').'?download='.$log);

        $this->assertResponseStatus(500);
    }

    public function testDownloadLogFile()
    {
        $logsPath = storage_path('logs/laravel.log');
        file_put_contents($logsPath, 'testing');
        
        $log = base64_encode('laravel.log');

        $response = $this->get(route('voyager.compass.index') . '?download=' . $log);

        $this->assertResponseStatus(200);
    }
}
