<?php

Route::group(['as' => 'voyager.'], function () {
    $namespace = '\\TCG\\Voyager\\Http\\Controllers\\';

    //Route::group(['middleware' => 'admin.user'], function () use ($namespace) {
        Route::view('/', 'voyager::dashboard')->name('dashboard');
    //});

    Route::get('login', ['uses' => $namespace.'AuthController@login', 'as' => 'login']);
    Route::post('login', ['uses' => $namespace.'AuthController@processLogin', 'as' => 'login']);
    Route::get('logout', ['uses' => $namespace.'AuthController@logout', 'as' => 'logout']);

    // Asset routes
    Route::get('voyager-assets', ['uses' => $namespace.'VoyagerController@assets', 'as' => 'voyager_assets']);
});
