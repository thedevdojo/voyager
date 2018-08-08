<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\User;

class UserProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected $editPageForTheCurrentUser;

    protected $profilePageForTheCurrentUser;

    protected $withDummy = true;

    public function setUp()
    {
        parent::setUp();

        $this->install();

        $this->user = Auth::loginUsingId(1);

        $this->editPageForTheCurrentUser = route('voyager.profile.edit');

        $this->profilePageForTheCurrentUser = route('voyager.profile');

        $this->withFactories(__DIR__.'/database/factories');
    }

    public function testCanSeeTheUserInfoOnHisProfilePage()
    {
        $this->visit($this->profilePageForTheCurrentUser)
             ->seeInElement('h4', $this->user->name)
             ->seeInElement('.user-email', $this->user->email)
             ->seeLink(__('voyager::profile.edit'));
    }

    public function testCanEditUserName()
    {
        $this->visit($this->profilePageForTheCurrentUser)
             ->click(__('voyager::profile.edit'))
             ->see(__('voyager::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('New Awesome Name', 'name')
             ->press(__('voyager::generic.save'))
             ->seePageIs($this->profilePageForTheCurrentUser)
             ->seeInDatabase(
                 'users',
                 ['name' => 'New Awesome Name']
             );
    }

    public function testCanEditUserEmail()
    {
        $this->visit($this->profilePageForTheCurrentUser)
             ->click(__('voyager::profile.edit'))
             ->see(__('voyager::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('another@email.com', 'email')
             ->press(__('voyager::generic.save'))
             ->seePageIs($this->profilePageForTheCurrentUser)
             ->seeInDatabase(
                 'users',
                 ['email' => 'another@email.com']
             );
    }

    public function testCanEditUserPassword()
    {
        $this->visit($this->profilePageForTheCurrentUser)
             ->click(__('voyager::profile.edit'))
             ->see(__('voyager::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('voyager-rocks', 'password')
             ->press(__('voyager::generic.save'))
             ->seePageIs($this->profilePageForTheCurrentUser);

        $updatedPassword = DB::table('users')->where('id', 1)->first()->password;
        $this->assertTrue(Hash::check('voyager-rocks', $updatedPassword));
    }

    public function testCanEditUserAvatar()
    {
        $this->visit($this->profilePageForTheCurrentUser)
             ->click(__('voyager::profile.edit'))
             ->see(__('voyager::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->attach($this->newImagePath(), 'avatar')
             ->press(__('voyager::generic.save'))
             ->seePageIs($this->profilePageForTheCurrentUser)
             ->dontSeeInDatabase(
                 'users',
                 ['id' => 1, 'avatar' => 'user/default.png']
             );
    }

    public function testCanEditUserEmailWithEditorPermissions()
    {
        $user = factory(\TCG\Voyager\Models\User::class)->create();
        $roleId = $user->role_id;
        $role = Role::find($roleId);
        // add permissions which reflect a possible editor role
        // without permissions to edit  users
        $role->permissions()->attach(\TCG\Voyager\Models\Permission::whereIn('key', [
            'browse_admin',
            'browse_users',
        ])->get()->pluck('id')->all());
        Auth::onceUsingId($user->id);
        $this->visit($this->profilePageForTheCurrentUser)
             ->click(__('voyager::profile.edit'))
             ->see(__('voyager::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->type('another@email.com', 'email')
             ->press(__('voyager::generic.save'))
             ->seePageIs($this->profilePageForTheCurrentUser)
             ->seeInDatabase(
                 'users',
                 ['email' => 'another@email.com']
             );
    }

    public function testCanSetUserLocale()
    {
        $this->visit($this->profilePageForTheCurrentUser)
             ->click(__('voyager::profile.edit'))
             ->see(__('voyager::profile.edit_user'))
             ->seePageIs($this->editPageForTheCurrentUser)
             ->select('de', 'locale')
             ->press(__('voyager::generic.save'));

        $user = User::find(1);
        $this->assertTrue(($user->locale == 'de'));
    }

    protected function newImagePath()
    {
        return realpath(__DIR__.'/temp/new_avatar.png');
    }
}
