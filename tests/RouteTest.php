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

        $prefix = config('voyager.routes.prefix');
        $urls = [
            "/{$prefix}",
            "/{$prefix}/roles",
            "/{$prefix}/roles/1",
            "/{$prefix}/roles/1/edit",
            "/{$prefix}/roles/create",
            "/{$prefix}/users",
            "/{$prefix}/users/create",
            "/{$prefix}/users/1",
            "/{$prefix}/users/1/edit",
            "/{$prefix}/media",
            "/{$prefix}/posts",
            "/{$prefix}/posts/create",
            "/{$prefix}/posts/23/edit",
            "/{$prefix}/posts/23",
            "/{$prefix}/pages",
            "/{$prefix}/pages/create",
            "/{$prefix}/pages/1",
            "/{$prefix}/pages/1/edit",
            "/{$prefix}/categories",
            "/{$prefix}/categories/create",
            "/{$prefix}/categories/1",
            "/{$prefix}/categories/1/edit",
            "/{$prefix}/menus",
            "/{$prefix}/menus/create",
            "/{$prefix}/menus/2/builder",
            "/{$prefix}/menus/2/edit",
            "/{$prefix}/database",
            "/{$prefix}/database/5/edit-bread",
            "/{$prefix}/database/edit-categories-table",
            "/{$prefix}/database/create-table",
            "/{$prefix}/settings",
        ];

        foreach ($urls as $url) {
            $response = $this->call('GET', $url);
            $this->assertEquals(200, $response->status(), $url.' did not return a 200');
        }
    }
}
