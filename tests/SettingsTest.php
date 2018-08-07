<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Setting;

class SettingsTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = Auth::loginUsingId(1);
    }

    public function testCanUpdateSettings()
    {
        $key = 'site.title';
        $newTitle = 'Just Another LaravelVoyager.com Site';

        $this->visit(route('voyager.settings.index'))
             ->seeInField($key, Setting::where('key', '=', $key)->first()->value)
             ->type($newTitle, $key)
             ->seeInElement('button', __('voyager::settings.save'))
             ->press(__('voyager::settings.save'))
             ->seePageIs(route('voyager.settings.index'));

        $this->assertEquals(
            Setting::where('key', '=', $key)->first()->value,
            $newTitle
        );
    }
}
