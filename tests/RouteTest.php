<?php

namespace TCG\Voyager\Tests;

class RouteTest extends TestCase
{
    protected $withDummy = true;

    public function setUp()
    {
        parent::setUp();

        $this->install();
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testGetRoutes()
    {
        $this->disableExceptionHandling();

        $this->visit(route('voyager.login'));
        $this->type('admin@admin.com', 'email');
        $this->type('password', 'password');
        $this->press('Login');

        $urls = [
            route('voyager.dashboard'),
            route('voyager.media.index'),
            route('voyager.settings.index'),
            route('voyager.roles.index'),
            route('voyager.roles.create'),
            route('voyager.roles.show', ['role' => 1]),
            route('voyager.roles.edit', ['role' => 1]),
            route('voyager.users.index'),
            route('voyager.users.create'),
            route('voyager.users.show', ['user' => 1]),
            route('voyager.users.edit', ['user' => 1]),
            route('voyager.posts.index'),
            route('voyager.posts.create'),
            route('voyager.posts.show', ['post' => 1]),
            route('voyager.posts.edit', ['post' => 1]),
            route('voyager.pages.index'),
            route('voyager.pages.create'),
            route('voyager.pages.show', ['page' => 1]),
            route('voyager.pages.edit', ['page' => 1]),
            route('voyager.categories.index'),
            route('voyager.categories.create'),
            route('voyager.categories.show', ['category' => 1]),
            route('voyager.categories.edit', ['category' => 1]),
            route('voyager.menus.index'),
            route('voyager.menus.create'),
            route('voyager.menus.show', ['menu' => 1]),
            route('voyager.menus.edit', ['menu' => 1]),
            route('voyager.database.index'),
            route('voyager.database.edit_bread', ['table' => 'categories']),
            route('voyager.database.edit', ['table' => 'categories']),
            route('voyager.database.create'),
        ];

        foreach ($urls as $url) {
            $response = $this->call('GET', $url);
            $this->assertEquals(200, $response->status(), $url.' did not return a 200');
        }
    }
}
