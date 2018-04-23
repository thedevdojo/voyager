<?php

namespace TCG\Voyager\Tests;

class LoginTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();
    }

    public function testSuccessfulLoginWithDefaultCredentials()
    {
        $this->visit(route('voyager.login'))
             ->type('admin@admin.com', 'email')
             ->type('password', 'password')
             ->press(__('voyager::generic.login'))
             ->seePageIs(route('voyager.dashboard'));
    }

    public function testShowAnErrorMessageWhenITryToLoginWithWrongCredentials()
    {
        $this->visit(route('voyager.login'))
             ->type('john@Doe.com', 'email')
             ->type('pass', 'password')
             ->press(__('voyager::generic.login'))
             ->seePageIs(route('voyager.login'))
             ->see(__('auth.failed'))
             ->seeInField('email', 'john@Doe.com');
    }
}
