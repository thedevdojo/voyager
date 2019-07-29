<?php

Route::group(['as' => 'voyager.'], function () {
    $namespace = '\\TCG\\Voyager\\Http\\Controllers\\';

    //Route::group(['middleware' => 'admin.user'], function () use ($namespace) {
    Route::view('/', 'voyager::dashboard')->name('dashboard');

    Route::group([
        'as'     => 'bread.',
        'prefix' => 'bread',
    ], function () use ($namespace) {
        Route::get('/', ['uses' => $namespace.'BreadManagerController@index', 'as' => 'index']);
        Route::get('create/{table}', ['uses' => $namespace.'BreadManagerController@create', 'as' => 'create']);
        Route::get('edit/{table}',   ['uses' => $namespace.'BreadManagerController@edit', 'as' => 'edit']);
        Route::put('{table}',   ['uses' => $namespace.'BreadManagerController@update', 'as' => 'update']);
        Route::delete('{table}',   ['uses' => $namespace.'BreadManagerController@edit', 'as' => 'delete']);
    });
    //});

    Route::get('login', ['uses' => $namespace.'AuthController@login', 'as' => 'login']);
    Route::post('login', ['uses' => $namespace.'AuthController@processLogin', 'as' => 'login']);
    Route::get('logout', ['uses' => $namespace.'AuthController@logout', 'as' => 'logout']);

    // Asset routes
    Route::get('voyager-assets', ['uses' => $namespace.'VoyagerController@assets', 'as' => 'voyager_assets']);
});
