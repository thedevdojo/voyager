<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserProfileTest extends TestCase
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

    public function testCanSeeTheUserInfoOnHisProfilePage()
    {
        $this->visit(route('voyager.profile'))
             ->seeInElement('h4', $this->user->name)
             ->seeInElement('.user-email', $this->user->email)
             ->seeLink('Edit My Profile');
    }

    public function testCanEditUserName()
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

    public function testCanEditUserEmail()
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

    public function testCanEditUserPassword()
    {
        $this->visit(route('voyager.profile'))
             ->click('Edit My Profile')
             ->see('Edit User')
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('voyager-rocks', 'password')
             ->press('Submit')
             ->seePageIs($this->listOfUsers);

        $updatedPassword = DB::table('users')->where('id', 1)->first()->password;
        $this->assertTrue(Hash::check('voyager-rocks', $updatedPassword));
    }

    public function testCanEditUserAvatar()
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
