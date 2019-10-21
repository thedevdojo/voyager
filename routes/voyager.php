<?php

$router->group(['as' => 'voyager.'], function () use ($router) {
    $namespace = '\\TCG\\Voyager\\Http\\Controllers\\';

    Route::group(['middleware' => 'voyager.admin'], function () use ($namespace, $router) {
        $router->view('/', 'voyager::dashboard')->name('dashboard');
        $router->post('search', ['uses' => $namespace.'VoyagerController@search', 'as' => 'search']);
        $router->post('search-relationship', ['uses' => $namespace.'VoyagerController@searchRelationship', 'as' => 'search-relationship']);
        $router->post('add-relationship', ['uses' => $namespace.'VoyagerController@addRelationship', 'as' => 'add-relationship']);

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
            Route::resource($bread->slug, $controller);
            Route::post($bread->slug.'/data', ['uses'=> $controller.'@data', 'as' => $bread->slug.'.data']);
        }

        // UI Routes
        $router->view('ui', 'voyager::ui.index')->name('ui');
    });

    $router->get('login', ['uses' => $namespace.'AuthController@login', 'as' => 'login']);
    $router->post('login', ['uses' => $namespace.'AuthController@processLogin', 'as' => 'login']);
    $router->get('logout', ['uses' => $namespace.'AuthController@logout', 'as' => 'logout']);

    // Asset routes
    $router->get('voyager-assets', ['uses' => $namespace.'VoyagerController@assets', 'as' => 'voyager_assets']);
});
