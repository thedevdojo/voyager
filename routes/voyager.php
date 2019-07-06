<?php

Route::group(['as' => 'voyager.'], function () {
    $namespace = '\\TCG\\Voyager\\Http\\Controllers\\';

    Route::get('/', function () {
        echo 'yeah!';
    });

    Route::view('/', 'voyager::dashboard');

    // Asset routes
    Route::get('voyager-assets', ['uses' => $namespace.'VoyagerController@assets', 'as' => 'voyager_assets']);
});
