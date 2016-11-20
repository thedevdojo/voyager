<?php

class AdminProfileTest extends TestCase
{
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

    // We can edit the user email.

    // We can edit the user password.
}
