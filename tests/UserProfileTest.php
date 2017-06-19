<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use TCG\Voyager\Models\Role;

class UserProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected $editPageForTheCurrentUser;

    protected $listOfUsers;

    protected $withDummy = true;

    public function setUp()
    {
        parent::setUp();

        $this->install();

        $this->user = Auth::loginUsingId(1);

        $this->editPageForTheCurrentUser = route('voyager.users.edit', ['user' => $this->user->id]);

        $this->listOfUsers = route('voyager.users.index');

        $this->withFactories(__DIR__.'/database/factories');
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
             ->press(__('voyager.generic.submit'))
             ->seePageIs($this->editPageForTheCurrentUser)
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
             ->press(__('voyager.generic.submit'))
             ->seePageIs($this->editPageForTheCurrentUser)
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
             ->press(__('voyager.generic.submit'))
             ->seePageIs($this->editPageForTheCurrentUser);

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
             ->press(__('voyager.generic.submit'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->dontSeeInDatabase(
                 'users',
                 ['id' => 1, 'avatar' => 'user/default.png']
             );
    }

    public function testCanEditUserEmailWithEditorPermissions()
    {
        $user = factory(\TCG\Voyager\Models\User::class)->create();
        $editPageForTheCurrentUser = route('voyager.users.edit', ['user' => $user->id]);
        $roleId = $user->role_id;
        $role = Role::find($roleId);
        // add permissions which reflect a possible editor role
        // without permissions to edit  users
        $role->permissions()->attach([1, 3, 12, 27, 32]);
        Auth::onceUsingId($user->id);
        $this->visit(route('voyager.profile'))
            ->click('Edit My Profile')
            ->see('Edit User')
            ->seePageIs($editPageForTheCurrentUser)
            ->type('another@email.com', 'email')
            ->press('Submit')
            ->seePageIs($editPageForTheCurrentUser)
            ->seeInDatabase(
                'users',
                ['email' => 'another@email.com']
            );
    }

    protected function newImagePath()
    {
        return realpath(__DIR__.'/temp/new_avatar.png');
    }
}
