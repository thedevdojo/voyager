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
    // We can visit the profile page and see the user info. eg: name & email.
    public function testCanSeeTheAdminInfoOnHisProfilePage()
    {
        $this->visit(route('voyager.profile'))
             ->seeInElement('h4', $this->user->name)
             ->seeInElement('.user-email', $this->user->email)
             ->seeLink('Edit My Profile');
    }

    // We can edit the user name.
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

    // We can edit the user email.

    // We can edit the user password.
}
