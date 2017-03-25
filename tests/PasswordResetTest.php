<?php
namespace TCG\Voyager\Tests;

use Notification;
use Illuminate\Auth\Passwords\PasswordBroker;
use TCG\Voyager\Models\User;
use TCG\Voyager\Notifications\ResetPassword;

class PasswordResetTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();
    }

    /** @test */
    public function user_can_send_password_reset_link_to_email_address()
    {
        Notification::fake();

        $user = User::first();

        $this->visit(route('voyager.login'))
             ->click('Forgot Your Password?')
             ->type($user->email, 'email')
             ->press('Send Password Reset Link')
             ->see(trans('passwords.sent'));

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /** @test */
    public function user_gets_error_message_when_user_does_not_exist()
    {
        Notification::fake();

        $user = User::first();

        $this->visit(route('voyager.login'))
             ->click('Forgot Your Password?')
             ->type('not-existing-user@test.org', 'email')
             ->press('Send Password Reset Link')
             ->see(trans('passwords.user'));

        Notification::assertNotSentTo($user, ResetPassword::class);
    }

    /** @test */
    public function user_can_reset_password_using_password_reset_link()
    {
        $user = User::first();
        $broker = $this->app->make(PasswordBroker::class);
        $token = $broker->createToken($user);

        $this->visit(route('voyager.password_reset_form', $token))
             ->type($user->email, 'email')
             ->type('password', 'password')
             ->type('password', 'password_confirmation')
             ->press('Reset Password')
             ->assertPageLoaded(trans('passwords.dashboard'));
    }

    /** @test */
    public function user_gets_error_message_when_password_reset_token_is_invalid()
    {
        $user = User::first();

        $this->visit(route('voyager.password_reset_form', 'not-existing-token'))
             ->type($user->email, 'email')
             ->type('password', 'password')
             ->type('password', 'password_confirmation')
             ->press('Reset Password')
             ->see(trans('passwords.token'));
    }
}
