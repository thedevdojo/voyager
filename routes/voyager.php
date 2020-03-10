<?php

Route::group(['as' => 'voyager.'], function () {
    $namespace = '\\TCG\\Voyager\\Http\\Controllers\\';

    Route::group(['middleware' => 'voyager.admin'], function () use ($namespace) {
        Route::view('/', 'voyager::dashboard')->name('dashboard');
        Route::post('search', ['uses' => $namespace.'VoyagerController@search', 'as' => 'search']);
        Route::post('search-relationship', ['uses' => $namespace.'VoyagerController@searchRelationship', 'as' => 'search-relationship']);
        Route::post('add-relationship', ['uses' => $namespace.'VoyagerController@addRelationship', 'as' => 'add-relationship']);
        Route::post('get-options', ['uses' => $namespace.'VoyagerController@getOptions', 'as' => 'get-options']);

        // BREAD manager
        Route::group([
            'as'     => 'bread.',
            'prefix' => 'bread',
        ], function () use ($namespace) {
            Route::get('/', ['uses' => $namespace.'BreadManagerController@index', 'as' => 'index']);
            Route::get('create/{table}', ['uses' => $namespace.'BreadManagerController@create', 'as' => 'create']);
            Route::get('edit/{table}', ['uses' => $namespace.'BreadManagerController@edit', 'as' => 'edit']);
            Route::put('{table}', ['uses' => $namespace.'BreadManagerController@update', 'as' => 'update']);
            Route::delete('{table}', ['uses' => $namespace.'BreadManagerController@destroy', 'as' => 'delete']);
        });

        // BREADs
        foreach (Bread::getBreads() as $bread) {
            $controller = $namespace.'BreadController';
            if (!empty($bread->controller)) {
                $controller = \Illuminate\Support\Str::start($bread->controller, '\\');
            }
            Route::group([
                'as'     => $bread->slug.'.',
                'prefix' => $bread->slug,
            ], function () use ($controller) {
                // Browse
                Route::get('/', ['uses' => $controller.'@browse', 'as' => 'browse']);
                Route::post('data', ['uses'=> $controller.'@data', 'as' => 'data']);

                // Edit
                Route::get('/edit/{id}', ['uses' => $controller.'@edit', 'as' => 'edit']);
                Route::put('/{id}', ['uses' => $controller.'@update', 'as' => 'update']);

                // Add
                Route::get('/add', ['uses' => $controller.'@add', 'as' => 'add']);
                Route::post('/', ['uses' => $controller.'@store', 'as' => 'store']);

                // Delete
                Route::delete('/{id}', ['uses' => $controller.'@delete', 'as' => 'delete']);

                // Read
                Route::get('/{id}', ['uses' => $controller.'@read', 'as' => 'read']);

                // Get relationship-items
                Route::post('/relationship-data', ['uses' => $controller.'@relationshipData', 'as' => 'relationship-data']);
                // Add relationship item
                Route::post('/relationship-add', ['uses' => $controller.'@relationshipAdd', 'as' => 'relationship-add']);
            });
        }

        // UI Routes
        Route::view('ui', 'voyager::ui.index')->name('ui');

        // Settings
        Route::get('settings', ['uses' => $namespace.'SettingsController@index', 'as' => 'settings.index']);
        Route::post('settings', ['uses' => $namespace.'SettingsController@store', 'as' => 'settings.store']);

        // Plugins
        Route::get('plugins', function () {
            return view('voyager::plugins.browse');
        })->name('plugins.index');
        Route::post('plugins/enable', ['uses' => $namespace.'PluginsController@enable', 'as' => 'plugins.enable']);
        Route::post('plugins', ['uses' => $namespace.'PluginsController@get', 'as' => 'plugins.get']);
        Route::get('plugins/settings/{key}', ['uses' => $namespace.'PluginsController@settings', 'as' => 'plugins.settings']);

        // Logout
        Route::get('logout', ['uses' => $namespace.'AuthController@logout', 'as' => 'logout']);
    });

    // Login
    Route::get('login', ['uses' => $namespace.'AuthController@login', 'as' => 'login']);
    Route::post('login', ['uses' => $namespace.'AuthController@processLogin', 'as' => 'login']);
    Route::post('forgot-password', ['uses' => $namespace.'AuthController@forgotPassword', 'as' => 'forgot_password']);

    // Asset routes
    Route::get('voyager-assets', ['uses' => $namespace.'VoyagerController@assets', 'as' => 'voyager_assets']);
});
