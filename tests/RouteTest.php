<?php

namespace TCG\Voyager\Tests;

class RouteTest extends TestCase
{
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
        $this->press(__('voyager::generic.login'));

        $urls = [
            route('voyager.dashboard'),
            route('voyager.media.index'),
            route('voyager.settings.index'),
            route('voyager.roles.index'),
            route('voyager.roles.create'),
            route('voyager.roles.show', 1),
            route('voyager.roles.edit', 1),
            route('voyager.users.index'),
            route('voyager.users.create'),
            route('voyager.users.show', 1),
            route('voyager.users.edit', 1),
            route('voyager.posts.index'),
            route('voyager.posts.create'),
            route('voyager.posts.show', 1),
            route('voyager.posts.edit', 1),
            route('voyager.pages.index'),
            route('voyager.pages.create'),
            route('voyager.pages.show', 1),
            route('voyager.pages.edit', 1),
            route('voyager.categories.index'),
            route('voyager.categories.create'),
            route('voyager.categories.show', 1),
            route('voyager.categories.edit', 1),
            route('voyager.menus.index'),
            route('voyager.menus.create'),
            route('voyager.menus.show', 1),
            route('voyager.menus.edit', 1),
            route('voyager.bread.edit', 'categories'),
            // Disabled as Doctrine DBAL is not supported in Laravel 11
            // route('voyager.database.index'),
            // route('voyager.database.edit', 'categories'),
            // route('voyager.database.create'),
        ];

        foreach ($urls as $url) {
            $response = $this->call('GET', $url);
            $this->assertEquals(200, $response->status(), $url.' did not return a 200');
        }
    }
}
