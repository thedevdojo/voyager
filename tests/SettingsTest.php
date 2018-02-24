<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use TCG\Voyager\Models\Role;

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
