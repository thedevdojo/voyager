<?php

/*
|--------------------------------------------------------------------------
| Voyager Routes
|--------------------------------------------------------------------------
|
| This file is where you may override any of the routes that are included
| with Voyager.
|
*/

Route::group(['as' => 'voyager.'], function() {
    $namespacePrefix = '\\TCG\\Voyager\\Http\\Controllers\\';

    Route::get('login', ['uses' => $namespacePrefix.'VoyagerAuthController@login', 'as' => 'login']);
    Route::post('login', ['uses' => $namespacePrefix.'VoyagerAuthController@postLogin', 'as' => 'postlogin']);

    Route::group(['middleware' => ['admin.user']], function() use ($namespacePrefix) {

        // Main Admin and Logout Route
        Route::get('/',       ['uses' => $namespacePrefix.'VoyagerController@index', 'as' => 'dashboard']);
        Route::get('logout',  ['uses' => $namespacePrefix.'VoyagerController@logout', 'as' => 'logout']);
        Route::post('upload', ['uses' => $namespacePrefix.'VoyagerController@upload', 'as' => 'upload']);

        Route::get('profile', ['uses' => $namespacePrefix.'VoyagerController@profile', 'as' => 'profile']);

        if (env('DB_CONNECTION') !== null && Schema::hasTable('data_types')) {
            foreach (\TCG\Voyager\Models\DataType::all() as $dataTypes) {
                Route::resource($dataTypes->slug, $namespacePrefix.'VoyagerBreadController');
            }
        }

        // Role Routes
        Route::resource('roles', $namespacePrefix.'VoyagerRoleController');

        // Menu Routes
        Route::group(['as' => 'menus.'], function() use($namespacePrefix) {
            Route::get('menus/{id}/builder/', ['uses' => $namespacePrefix.'VoyagerMenuController@builder', 'as' => 'builder']);

            Route::group(['prefix' => 'menus'], function () use ($namespacePrefix) {
                Route::delete('item/{id}', ['uses' => $namespacePrefix.'VoyagerMenuController@delete_menu', 'as' => 'delete_menu_item']);
                Route::post('item',        ['uses' => $namespacePrefix.'VoyagerMenuController@add_item', 'as' => 'add_item']);
                Route::put('item',         ['uses' => $namespacePrefix.'VoyagerMenuController@update_item', 'as' => 'update_menu_item']);
                Route::post('order',       ['uses' => $namespacePrefix.'VoyagerMenuController@order_item', 'as' => 'order_item']);
            });
        });

        // Settings
        Route::group([
            'as' => 'settings.',
            'prefix' => 'settings'
        ], function() use ($namespacePrefix) {
            Route::get('/',                 ['uses' => $namespacePrefix.'VoyagerSettingsController@index', 'as' => 'index']);
            Route::post('/',                ['uses' => $namespacePrefix.'VoyagerSettingsController@save', 'as' => 'store']);
            Route::post('/create',          ['uses' => $namespacePrefix.'VoyagerSettingsController@create', 'as' => 'create']);
            Route::delete('{id}',           ['uses' => $namespacePrefix.'VoyagerSettingsController@delete', 'as' => 'delete']);
            Route::get('{id}/move_up',      ['uses' => $namespacePrefix.'VoyagerSettingsController@move_up', 'as' => 'move_up']);
            Route::get('{id}/move_down',    ['uses' => $namespacePrefix.'VoyagerSettingsController@move_down', 'as' => 'move_down']);
            Route::get('{id}/delete_value', ['uses' => $namespacePrefix.'VoyagerSettingsController@delete_value', 'as' => 'delete_value']);
        });

        // Admin Media
        Route::group([
            'as' => 'media.',
            'prefix' => 'media'
        ], function() use ($namespacePrefix) {
            Route::get('/',                   ['uses' => $namespacePrefix.'VoyagerMediaController@index', 'as' => 'index']);
            Route::post('files',              ['uses' => $namespacePrefix.'VoyagerMediaController@files', 'as' => 'files']);
            Route::post('new_folder',         ['uses' => $namespacePrefix.'VoyagerMediaController@new_folder', 'as' => 'new_folder']);
            Route::post('delete_file_folder', ['uses' => $namespacePrefix.'VoyagerMediaController@delete_file_folder', 'as' => 'delete_file_folder']);
            Route::post('directories',        ['uses' => $namespacePrefix.'VoyagerMediaController@get_all_dirs', 'as' => 'get_all_dirs']);
            Route::post('move_file',          ['uses' => $namespacePrefix.'VoyagerMediaController@move_file', 'as' => 'move_file']);
            Route::post('rename_file',        ['uses' => $namespacePrefix.'VoyagerMediaController@rename_file', 'as' => 'rename_file']);
            Route::post('upload',             ['uses' => $namespacePrefix.'VoyagerMediaController@upload', 'as' => 'upload']);
        });

        // Database Routes
        Route::group([
            'as' => 'database.',
            'prefix' => 'database'
        ], function() use ($namespacePrefix) {
            Route::get('/',                  ['uses' => $namespacePrefix.'VoyagerDatabaseController@index', 'as' => 'index']);
            Route::get('table/create',       ['uses' => $namespacePrefix.'VoyagerDatabaseController@create', 'as' => 'create_table']);
            Route::post('table/create',      ['uses' => $namespacePrefix.'VoyagerDatabaseController@store', 'as' => 'store_table']);
            Route::get('table/{table}',      ['uses' => $namespacePrefix.'VoyagerDatabaseController@table', 'as' => 'browse_table']);
            Route::delete('table/{table}',   ['uses' => $namespacePrefix.'VoyagerDatabaseController@delete', 'as' => 'destroy_table']);
            Route::get('table/{table}/edit', ['uses' => $namespacePrefix.'VoyagerDatabaseController@edit', 'as' => 'edit_table']);
            Route::post('table/{table}',     ['uses' => $namespacePrefix.'VoyagerDatabaseController@update', 'as' => 'update_table']);

            Route::post('bread/create',      ['uses' => $namespacePrefix.'VoyagerDatabaseController@addBread', 'as' => 'create_bread']);
            Route::post('bread/',            ['uses' => $namespacePrefix.'VoyagerDatabaseController@storeBread', 'as' => 'store_bread']);
            Route::get('bread/{id}/edit',    ['uses' => $namespacePrefix.'VoyagerDatabaseController@addEditBread', 'as' => 'edit_bread']);
            Route::put('bread/{id}',         ['uses' => $namespacePrefix.'VoyagerDatabaseController@updateBread', 'as' => 'update_bread']);
            Route::delete('bread/{id}',      ['uses' => $namespacePrefix.'VoyagerDatabaseController@deleteBread', 'as' => 'delete_bread']);
        });
    });
});