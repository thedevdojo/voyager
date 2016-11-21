<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = Auth::loginUsingId(1);
    }

    public function testCanSeeTheAdminInfoOnHisProfilePage()
    {
        $this->visit(route('voyager.profile'))
             ->seeInElement('h4', $this->user->name)
             ->seeInElement('.user-email', $this->user->email)
             ->seeLink('Edit My Profile');
    }

    public function testCanEditAdminName()
    {
        $this->visit(route('voyager.profile'))
             ->click('Edit My Profile')
             ->see('Edit User')
             ->seePageIs(config('voyager.routes.prefix') . "/users/{$this->user->id}/edit")
             ->type('New Awesome Name', 'name')
             ->press('Update')
             ->seePageIs(config('voyager.routes.prefix') . '/users')
             ->seeInDatabase('users', ['name' => 'New Awesome Name']);
    }

    public function testCanEditAdminEmail()
    {
        $this->visit(route('voyager.profile'))
             ->click('Edit My Profile')
             ->see('Edit User')
             ->seePageIs(config('voyager.routes.prefix') . "/users/{$this->user->id}/edit")
             ->type('another@email.com', 'email')
             ->press('Update')
             ->seePageIs(config('voyager.routes.prefix') . '/users')
             ->seeInDatabase('users', ['email' => 'another@email.com']);
    }

    public function testCanEditAdminPassword()
    {
        $this->visit(route('voyager.profile'))
             ->click('Edit My Profile')
             ->see('Edit User')
             ->seePageIs(config('voyager.routes.prefix') . "/users/{$this->user->id}/edit")
             ->type('new_password', 'password')
             ->press('Update')
             ->seePageIs(config('voyager.routes.prefix') . '/users');

        $updatedPassword = DB::table('users')->where('id', 1)->first()->password;
        $this->assertTrue(Hash::check('new_password', $updatedPassword));
    }

    public function testCanEditAdminAvatar()
    {
        $this->visit(route('voyager.profile'))
             ->click('Edit My Profile')
             ->see('Edit User')
             ->seePageIs(config('voyager.routes.prefix') . "/users/{$this->user->id}/edit")
             ->attach(realpath(__DIR__ . '/temp/new_avatar.png'), 'avatar')
             ->press('Update')
             ->seePageIs(config('voyager.routes.prefix') . '/users')
             ->dontSeeInDatabase('users', ['id' => 1, 'avatar' => 'user/default.png']);
    }
}
