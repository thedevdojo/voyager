<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TCG\Voyager\Models\Role;

class RolesTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testRoles()
    {
        $this->visit('/admin')->see('Voyager');
        $this->visit('/admin/login');
        $this->visit('/admin/login');
        $this->type('admin@admin.com', 'email');
        $this->type('password', 'password');
        $this->press('Login Logging in');
        $this->seePageIs('/admin');


        // Adding a New Role
        $this->visit('/admin/roles')->click('Add New')->seePageIs('/admin/roles/create');
        $this->type('superadmin', 'name');
        $this->type('Super Admin', 'display_name');
        $this->press('Submit');
        $this->seePageIs('/admin/roles');
        $this->seeInDatabase('roles', ['name' => 'superadmin']);

        // Editing a Role
        $this->visit('/admin/roles/2/edit');
        $this->type('regular_user', 'name');
        $this->press('Submit');
        $this->seePageIs('/admin/roles');
        $this->seeInDatabase('roles', ['name' => 'regular_user']);

	    // Editing a Role
        $this->visit('/admin/roles/2/edit');
        $this->type('user', 'name');
        $this->press('Submit');
        $this->seePageIs('/admin/roles');
        $this->seeInDatabase('roles', ['name' => 'user']);

        // Get the current super admin role
        $superadmin_role = Role::where('name', '=', 'superadmin')->first();

	    // Deleting a Role
	    $response = $this->call('DELETE', '/admin/roles/'. $superadmin_role->id, ['_token' => csrf_token()]);
	    $this->assertEquals(302, $response->getStatusCode());
        $this->notSeeInDatabase('roles', ['name' => 'superadmin']);
        
    }

}
