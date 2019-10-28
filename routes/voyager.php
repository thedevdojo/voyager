<?php

$router->group(['as' => 'voyager.'], function () use ($router) {
    $namespace = '\\TCG\\Voyager\\Http\\Controllers\\';

    Route::group(['middleware' => 'voyager.admin'], function () use ($namespace, $router) {
        $router->view('/', 'voyager::dashboard')->name('dashboard');
        $router->post('search', ['uses' => $namespace.'VoyagerController@search', 'as' => 'search']);
        $router->post('search-relationship', ['uses' => $namespace.'VoyagerController@searchRelationship', 'as' => 'search-relationship']);
        $router->post('add-relationship', ['uses' => $namespace.'VoyagerController@addRelationship', 'as' => 'add-relationship']);
        $router->post('get-options', ['uses' => $namespace.'VoyagerController@getOptions', 'as' => 'get-options']);

        // BREAD manager
        $router->group([
            'as'     => 'bread.',
            'prefix' => 'bread',
        ], function () use ($namespace, $router) {
            Route::get('/', ['uses' => $namespace.'BreadManagerController@index', 'as' => 'index']);
            $router->get('create/{table}', ['uses' => $namespace.'BreadManagerController@create', 'as' => 'create']);
            $router->get('edit/{table}', ['uses' => $namespace.'BreadManagerController@edit', 'as' => 'edit']);
            $router->put('{table}', ['uses' => $namespace.'BreadManagerController@update', 'as' => 'update']);
            $router->delete('{table}', ['uses' => $namespace.'BreadManagerController@destroy', 'as' => 'delete']);
        });

        // BREADs
        foreach (Bread::getBreads() as $bread) {
            $controller = $namespace.'BreadController';
            if (!empty($bread->controller)) {
                $controller = \Illuminate\Support\Str::start($bread->controller, '\\');
            }
            $router->group([
                'as'     => $bread->slug.'.',
                'prefix' => $bread->slug,
            ], function () use ($bread, $controller, $router) {
                // Browse
                $router->get('/', ['uses' => $controller.'@browse', 'as' => 'browse']);
                $router->post('data', ['uses'=> $controller.'@data', 'as' => 'data']);

                // Edit
                $router->get('/edit/{id}', ['uses' => $controller.'@edit', 'as' => 'edit']);
                $router->put('/{id}', ['uses' => $controller.'@update', 'as' => 'update']);

                // Add
                $router->get('/add', ['uses' => $controller.'@add', 'as' => 'add']);
                $router->post('/', ['uses' => $controller.'@store', 'as' => 'store']);

                // Delete
                $router->delete('/{id}', ['uses' => $controller.'@delete', 'as' => 'delete']);

                // Read
                $router->get('/{id}', ['uses' => $controller.'@read', 'as' => 'read']);
            });
        }

        // UI Routes
        $router->view('ui', 'voyager::ui.index')->name('ui');
    });

    // Settings
    $router->get('settings', ['uses' => $namespace.'SettingsController@index', 'as' => 'settings.index']);
    $router->post('settings', ['uses' => $namespace.'SettingsController@store', 'as' => 'settings.store']);

    // Login/Logout
    $router->get('login', ['uses' => $namespace.'AuthController@login', 'as' => 'login']);
    $router->post('login', ['uses' => $namespace.'AuthController@processLogin', 'as' => 'login']);
    $router->get('logout', ['uses' => $namespace.'AuthController@logout', 'as' => 'logout']);

    // Asset routes
    $router->get('voyager-assets', ['uses' => $namespace.'VoyagerController@assets', 'as' => 'voyager_assets']);
});
