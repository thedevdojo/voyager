<?php

namespace TCG\Voyager\Tests;

use Auth;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TCG\Voyager\Http\Middleware\VoyagerAdminMiddleware;
use TCG\Voyager\Models\User;

class CustomGuardTest extends TestCase
{
    /**
     * Admin middleware uses custom guard.
     *
     * This test will make sure that the VoyagerAdminMiddleware uses
     * the custom guard.
     */
    public function testAdminMiddlewareUsesCustomGuard()
    {
        $this->useAuthorizedCustomGuard();

        $middleware = new VoyagerAdminMiddleware();
        $response = new Response();

        $actualResponse = $middleware->handle(new Request(), function () use ($response) {
            return $response;
        });

        // Correct guard usage will return our $response.
        // Incorrect guard usage will redirect us to login
        $this->assertSame($actualResponse, $response);
    }

    /**
     * Voyager postLogin uses custom guard.
     *
     * This test will make sure that the postLogin uses custom guard
     * for authorizing users.
     */
    public function testVoyagerPostLoginUsesCustomGuard()
    {
        $loginData = [
            'email'    => 'voyager@voyager.com',
            'password' => 'voyager',
        ];

        $guard = $this->getMockBuilder(StatefulGuard::class)
            ->setMethods(['guest', 'user', 'attempt'])
            ->getMockForAbstractClass();

        $guard->method('guest')
            ->will($this->returnValue(true));

        $guard->method('user')
            ->will($this->returnValue(null));

        $guard->method('attempt')
            ->will($this->returnValueMap([
                [$loginData, false, true],
            ]));

        $this->app->instance('VoyagerAuth', $guard);

        $response = $this->json('POST', route('voyager.postlogin'), $loginData);

        // Correct guard usage will redirect us to the dashboard.
        // Incorrect guard, and failed login will redirect us to login.
        $response->assertRedirectedTo(route('voyager.dashboard'));
    }

    /**
     * Voyager controllers use custom guard.
     *
     * This test will make sure that controllers internal authorize()
     * function uses the custom guard.
     */
    public function testVoyagerControllersUseCustomGuard()
    {
        $this->useAuthorizedCustomGuard();

        $response = $this->action('GET', 'TCG\Voyager\Http\Controllers\VoyagerBaseController@index');

        // Incorrect guard usage will redirect to login
        $this->assertResponseOk($response);
    }

    /**
     * Default guard is used when custom is not defined.
     *
     * This test will make sure that VoyagerAuth defaults to the applications
     * default guard when no custom guard is defined.
     */
    public function testDefaultGuardIsUsedWhenCustomIsNotDefined()
    {
        $this->assertSame($this->app->get('VoyagerAuth')->guard(), Auth::guard());
    }

    /**
     * Return mock user where hasPermission always returns true.
     */
    protected function getMockUserWithAllPermissions()
    {
        $user = $this->getMockBuilder(User::class)
            ->setMethods(['hasPermission'])
            ->getMock();

        $user->method('hasPermission')
            ->will($this->returnValue(true));

        return $user;
    }

    /**
     * Sets the application to use custom guard which is authenticated
     * with an mocked admin user.
     */
    protected function useAuthorizedCustomGuard()
    {
        $user = $this->getMockUserWithAllPermissions();

        $guard = $this->getMockBuilder(StatefulGuard::class)
            ->setMethods(['guest', 'check', 'user'])
            ->getMockForAbstractClass();

        $guard->method('guest')
            ->will($this->returnValue(false));

        $guard->method('check')
            ->will($this->returnValue(false));

        $guard->method('user')
            ->will($this->returnValue($user));

        $this->app->instance('VoyagerAuth', $guard);

        return $guard;
    }
}
