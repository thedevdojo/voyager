<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;

class SettingsTest extends TestCase
{
    protected $user;

    protected $withDummy = true;

    public function setUp()
    {
        parent::setUp();

        $this->install();

        $this->user = Auth::loginUsingId(1);
    }

    public function testCanUpdateSettings()
    {
        $this->visit(route('voyager.settings.index'))
             ->seeInElement('button', __('voyager::voyager.settings.save'))
             ->press(__('voyager::voyager.settings.save'))
             ->seePageIs(route('voyager.settings.index'));
    }
}
