<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected $editPageForTheCurrentUser;

    protected $listOfUsers;

    public function setUp()
    {
        parent::setUp();

        $this->user = Auth::loginUsingId(1);

        $this->editPageForTheCurrentUser = config('voyager.routes.prefix') . "/users/{$this->user->id}/edit";

        $this->listOfUsers = config('voyager.routes.prefix') . '/users';
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
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('New Awesome Name', 'name')
             ->press('Submit')
             ->seePageIs($this->listOfUsers)
             ->seeInDatabase(
                 'users',
                 ['name' => 'New Awesome Name']
             );
    }

    public function testCanEditAdminEmail()
    {
        $this->visit(route('voyager.profile'))
             ->click('Edit My Profile')
             ->see('Edit User')
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('another@email.com', 'email')
             ->press('Submit')
             ->seePageIs($this->listOfUsers)
             ->seeInDatabase(
                 'users',
                 ['email' => 'another@email.com']
             );
    }

    public function testCanEditAdminPassword()
    {
        $this->visit(route('voyager.profile'))
             ->click('Edit My Profile')
             ->see('Edit User')
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('new_password', 'password')
             ->press('Submit')
             ->seePageIs($this->listOfUsers);

        $updatedPassword = DB::table('users')->where('id', 1)->first()->password;
        $this->assertTrue(Hash::check('new_password', $updatedPassword));
    }

    public function testCanEditAdminAvatar()
    {
        $this->visit(route('voyager.profile'))
             ->click('Edit My Profile')
             ->see('Edit User')
             ->seePageIs($this->editPageForTheCurrentUser)
             ->attach($this->newImagePath(), 'avatar')
             ->press('Submit')
             ->seePageIs($this->listOfUsers)
             ->dontSeeInDatabase(
                 'users',
                 ['id' => 1, 'avatar' => 'user/default.png']
             );
    }

    protected function newImagePath()
    {
        return realpath(__DIR__ . '/temp/new_avatar.png');
    }
}
