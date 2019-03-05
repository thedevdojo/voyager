<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;

class AssetsTest extends TestCase
{
    public function testCanLoadVoyagerCss()
    {
        $response = $this->call('GET', voyager_asset('css/app.css'));
        $this->assertEquals(200, $response->status());
    }

    public function testCanLoadVoyagerJs()
    {
        $response = $this->call('GET', voyager_asset('js/app.js'));
        $this->assertEquals(200, $response->status());
    }

    public function testCannotLoadAssets()
    {
        $response = $this->call('GET', voyager_asset('not_existing_file.css'));
        $this->assertEquals(404, $response->status());
    }

    public function testCannotLoadFileOutside()
    {
        $response = $this->call('GET', voyager_asset('../src/Voyager.php'));
        $this->assertEquals(404, $response->status());
    }
}
