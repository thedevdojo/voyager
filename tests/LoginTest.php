<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testLogin()
    {
        $this->visit('/')->see('Laravel');
        $this->visit('/admin')->see('Voyager');
        $this->visit('/admin/login');
        $this->visit('/admin/login');
        $this->type('admin@admin.com', 'email');
        $this->type('password', 'password');
        $this->press('Login Logging in');
        $this->seePageIs('/admin');
    }

}
