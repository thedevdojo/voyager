<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;

class LoginTest extends TestCase
{
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
        session()->setPreviousUrl(route('voyager.login'));

        $this->visit(route('voyager.login'))
             ->type('john@Doe.com', 'email')
             ->type('pass', 'password')
             ->press(__('voyager::generic.login'))
             ->seePageIs(route('voyager.login'))
             ->see(__('auth.failed'))
             ->seeInField('email', 'john@Doe.com');
    }

    public function testRedirectIfLoggedIn()
    {
        Auth::loginUsingId(1);

        $this->visit(route('voyager.login'))
             ->seePageIs(route('voyager.dashboard'));
    }

    public function testRedirectIfNotLoggedIn()
    {
        $this->visit(route('voyager.profile'))
             ->seePageIs(route('voyager.login'));
    }

    public function testCanLogout()
    {
        Auth::loginUsingId(1);

        $this->visit(route('voyager.dashboard'))
             ->press(__('voyager::generic.logout'))
             ->seePageIs(route('voyager.login'));
    }

    public function testGetsLockedOutAfterFiveAttempts()
    {
        session()->setPreviousUrl(route('voyager.login'));

        for ($i = 0; $i <= 6; $i++) {
            $t = $this->visit(route('voyager.login'))
                 ->type('john@Doe.com', 'email')
                 ->type('pass', 'password')
                 ->press(__('voyager::generic.login'));
        }

        $t->see('Too many login attempts. Please try again in');
    }
}
