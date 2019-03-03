<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Hash;
use TCG\Voyager\Models\User;

class CommandsTest extends TestCase
{
    public function testCanExecuteMakeModelCommand()
    {
        User::create([
            'name'     => 'Test',
            'email'    => 'my@email.com',
            'password' => Hash::make('password'),
        ]);

        $code = $this->artisan('voyager:admin my@email.com');
        $this->assertEquals($code, 0);

        $user = User::where('email', 'my@email.com')->first();
        $this->assertTrue($user->hasPermission('browse_admin'));
    }
}
