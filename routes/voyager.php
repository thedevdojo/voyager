<?php

$router->group(['as' => 'voyager.'], function () use ($router) {
    $namespace = '\\TCG\\Voyager\\Http\\Controllers\\';

    //Route::group(['middleware' => 'admin.user'], function () use ($namespace) {
    $router->view('/', 'voyager::dashboard')->name('dashboard');

    // BREAD manager
    $router->group([
        'as'     => 'bread.',
        'prefix' => 'bread',
    ], function () use ($namespace, $router) {
        Route::get('/', ['uses' => $namespace.'BreadManagerController@index', 'as' => 'index']);
        $router->get('create/{table}', ['uses' => $namespace.'BreadManagerController@create', 'as' => 'create']);
        $router->get('edit/{table}', ['uses' => $namespace.'BreadManagerController@edit', 'as' => 'edit']);
        $router->put('{table}', ['uses' => $namespace.'BreadManagerController@update', 'as' => 'update']);
        $router->delete('{table}', ['uses' => $namespace.'BreadManagerController@edit', 'as' => 'delete']);
    });

    // BREADs
    foreach (Voyager::getBreads() as $bread) {
        $controller = $namespace.'BreadController';
        if (!empty($bread->controller)) {
            $controller = \Illuminate\Support\Str::start($bread->controller, '\\');
        }
        Route::resource($bread->slug, $controller);
    }

    //});

    $router->get('login', ['uses' => $namespace.'AuthController@login', 'as' => 'login']);
    $router->post('login', ['uses' => $namespace.'AuthController@processLogin', 'as' => 'login']);
    $router->get('logout', ['uses' => $namespace.'AuthController@logout', 'as' => 'logout']);

    // UI Routes
    $router->view('ui', 'voyager::ui.index')->name('ui');

    // Asset routes
    $router->get('voyager-assets', ['uses' => $namespace.'VoyagerController@assets', 'as' => 'voyager_assets']);
});
