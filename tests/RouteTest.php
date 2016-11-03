<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RouteTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testGetRoutes()
    {

        $this->visit('/admin')->see('Voyager');
        $this->visit('/admin/login');
        $this->visit('/admin/login');
        $this->type('admin@admin.com', 'email');
        $this->type('password', 'password');
        $this->press('Login Logging in');
        $this->seePageIs('/admin');

        $urls = [
            '/admin',
            '/admin/roles',
            '/admin/roles/1',
            '/admin/roles/1/edit',
            '/admin/roles/create',
            '/admin/users',
            '/admin/users/create',
            '/admin/users/1',
            '/admin/users/1/edit',
            '/admin/media',
            '/admin/posts',
            '/admin/posts/create',
            '/admin/posts/23/edit',
            '/admin/posts/23',
            '/admin/pages',
            '/admin/pages/create',
            '/admin/pages/1',
            '/admin/pages/1/edit',
            '/admin/categories',
            '/admin/categories/create',
            '/admin/categories/1',
            '/admin/categories/1/edit',
            '/admin/menus',
            '/admin/menus/create',
            '/admin/menus/2/builder',
            '/admin/menus/2/edit',
            '/admin/database',
            '/admin/database/5/edit-bread',
            '/admin/database/edit-categories-table',
            '/admin/database/create-table',
            '/admin/settings'
            ];

        foreach($urls as $url){
            $response = $this->call('GET', $url);
            $this->assertEquals(200, $response->status(),  $url . " did not return a 200");
        }
        
    }

}
