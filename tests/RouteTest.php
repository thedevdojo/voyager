<?php


class RouteTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testGetRoutes()
    {
        $this->visit(route('voyager.login'));
        $this->type('admin@admin.com', 'email');
        $this->type('password', 'password');
        $this->press('Login Logging in');

        $urls = [
            route('voyager.dashboard'),
            route('voyager.media'),
            route('voyager.settings'),
            route('roles.index'),
            route('roles.create'),
            route('roles.show', ['role' => 1]),
            route('roles.edit', ['role' => 1]),
            route('users.index'),
            route('users.create'),
            route('users.show', ['user' => 1]),
            route('users.edit', ['user' => 1]),
            route('posts.index'),
            route('posts.create'),
            route('posts.show', ['post' => 1]),
            route('posts.edit', ['post' => 1]),
            route('pages.index'),
            route('pages.create'),
            route('pages.show', ['page' => 1]),
            route('pages.edit', ['page' => 1]),
            route('categories.index'),
            route('categories.create'),
            route('categories.show', ['category' => 1]),
            route('categories.edit', ['category' => 1]),
            route('menus.index'),
            route('menus.create'),
            route('menus.show', ['menu' => 2]),
            route('menus.edit', ['menu' => 2]),
            route('voyager.database'),
            route('voyager.database.edit_bread', ['id' => 5]),
            route('voyager.database.edit_table', ['table' => 'categories']),
            route('voyager.database.create_table'),
        ];

        foreach ($urls as $url) {
            $response = $this->call('GET', $url);
            $this->assertEquals(200, $response->status(), $url.' did not return a 200');
        }
    }
}
