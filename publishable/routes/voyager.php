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

Route::group([
    'namespace'  => '\\TCG\\Voyager\\Http\\Controllers',
    'middleware' => 'web',
    'prefix'     => 'admin',
], function () {
    Route::get('login', ['uses' => 'VoyagerAuthController@login', 'as' => 'voyager.login']);
    Route::post('login', ['uses' => 'VoyagerAuthController@postLogin']);

    Route::group(['middleware' => ['admin.user']], function () {

        // Main Admin and Logout Route
        Route::get('/', ['uses' => 'VoyagerController@index', 'as' => 'voyager.dashboard']);
        Route::get('logout', ['uses' => 'VoyagerController@logout', 'as' => 'voyager.logout']);
        Route::post('upload', ['uses' => 'VoyagerController@upload', 'as' => 'voyager.upload']);

        Route::get('profile', ['uses' => 'VoyagerController@profile', 'as' => 'voyager.profile']);

        if (env('DB_CONNECTION') !== null && Schema::hasTable('data_types')) {
            foreach (\TCG\Voyager\Models\DataType::all() as $dataTypes) {
                Route::resource($dataTypes->slug, 'VoyagerBreadController');
            }
        }

        // Role Routes
        Route::resource('roles', 'VoyagerRoleController');

        // Menu Routes
        Route::get('menus/{id}/builder/', ['uses' => 'VoyagerMenuController@builder', 'as' => 'voyager.menu.builder']);

        Route::group(['prefix' => 'menu'], function () {
            Route::delete('delete_menu_item/{id}', ['uses' => 'VoyagerMenuController@delete_menu', 'as' => 'voyager.menu.delete_menu_item']);
            Route::post('add_item', ['uses' => 'VoyagerMenuController@add_item', 'as' => 'voyager.menu.add_item']);
            Route::put('update_menu_item', ['uses' => 'VoyagerMenuController@update_item', 'as' => 'voyager.menu.update_menu_item']);
            Route::post('order', ['uses' => 'VoyagerMenuController@order_item', 'as' => 'voyager.menu.order_item']);
        });

        // Settings
        Route::group(['prefix' => 'settings'], function () {
            Route::get('/', ['uses' => 'VoyagerSettingsController@index', 'as' => 'voyager.settings']);
            Route::post('/', ['uses' => 'VoyagerSettingsController@save']);
            Route::post('create', ['uses' => 'VoyagerSettingsController@create', 'as' => 'voyager.settings.create']);
            Route::delete('{id}', ['uses' => 'VoyagerSettingsController@delete', 'as' => 'voyager.settings.delete']);
            Route::get('move_up/{id}', ['uses' => 'VoyagerSettingsController@move_up', 'as' => 'voyager.settings.move_up']);
            Route::get('move_down/{id}', ['uses' => 'VoyagerSettingsController@move_down', 'as' => 'voyager.settings.move_down']);
            Route::get('delete_value/{id}', ['uses' => 'VoyagerSettingsController@delete_value', 'as' => 'voyager.settings.delete_value']);
        });

        // Admin Media
        Route::group(['prefix' => 'media'], function () {
            Route::get('/', ['uses' => 'VoyagerMediaController@index', 'as' => 'voyager.media']);
            Route::post('files', ['uses' => 'VoyagerMediaController@files']);
            Route::post('new_folder', ['uses' => 'VoyagerMediaController@new_folder']);
            Route::post('delete_file_folder', ['uses' => 'VoyagerMediaController@delete_file_folder']);
            Route::post('directories', ['uses' => 'VoyagerMediaController@get_all_dirs']);
            Route::post('move_file', ['uses' => 'VoyagerMediaController@move_file']);
            Route::post('rename_file', ['uses' => 'VoyagerMediaController@rename_file']);
            Route::post('upload', ['uses' => 'VoyagerMediaController@upload', 'as' => 'voyager.media.upload']);
        });

        // Database Routes
        Route::group(['prefix' => 'database'], function () {
            Route::get('/', ['uses' => 'VoyagerDatabaseController@index', 'as' => 'voyager.database']);
            Route::get('create-table', ['uses' => 'VoyagerDatabaseController@create', 'as' => 'voyager.database.create_table']);
            Route::post('create-table', ['uses' => 'VoyagerDatabaseController@store']);
            Route::get('table/{table}', ['uses' => 'VoyagerDatabaseController@table', 'as' => 'voyager.database.browse_table']);
            Route::delete('table/delete/{table}', ['uses' => 'VoyagerDatabaseController@delete']);
            Route::get('edit-{table}-table', ['uses' => 'VoyagerDatabaseController@edit', 'as' => 'voyager.database.edit_table']);
            Route::post('edit-{table}-table', ['uses' => 'VoyagerDatabaseController@update']);

            Route::post('create_bread', ['uses' => 'VoyagerDatabaseController@addBread', 'as' => 'voyager.database.create_bread']);
            Route::post('store_bread', ['uses' => 'VoyagerDatabaseController@storeBread', 'as' => 'voyager.database.store_bread']);
            Route::get('{id}/edit-bread', ['uses' => 'VoyagerDatabaseController@addEditBread', 'as' => 'voyager.database.edit_bread']);
            Route::put('{id}/edit-bread', ['uses' => 'VoyagerDatabaseController@updateBread']);
            Route::delete('delete_bread/{id}', ['uses' => 'VoyagerDatabaseController@deleteBread', 'as' => 'voyager.database.delete_bread']);
        });
    });
});
