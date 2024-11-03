<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use TCG\Voyager\Events\Routing;
use TCG\Voyager\Events\RoutingAdmin;
use TCG\Voyager\Events\RoutingAdminAfter;
use TCG\Voyager\Events\RoutingAfter;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Voyager Routes
|--------------------------------------------------------------------------
|
| This file is where you may override any of the routes that are included
| with Voyager.
|
*/

Route::group(['as' => 'voyager.'], function () {
    event(new Routing());

    $namespacePrefix = config('voyager.controllers.namespace');

    Route::get('login', [$namespacePrefix . '\VoyagerAuthController', 'login'])->name('login');
    Route::post('login', [$namespacePrefix . '\VoyagerAuthController', 'postLogin'])->name('postlogin');

    Route::group(['middleware' => 'admin.user'], function () use ($namespacePrefix) {
        event(new RoutingAdmin());

        // Main Admin and Logout Route
        Route::get('/', [$namespacePrefix . '\VoyagerController', 'index'])->name('dashboard');
        Route::post('logout', [$namespacePrefix . '\VoyagerController', 'logout'])->name('logout');
        Route::post('upload', [$namespacePrefix . '\VoyagerController', 'upload'])->name('upload');

        Route::get('profile', [$namespacePrefix . '\VoyagerUserController', 'profile'])->name('profile');

        try {
            foreach (Voyager::model('DataType')::all() as $dataType) {
                $breadController = $dataType->controller
                    ? Str::start($dataType->controller, '\\')
                    : $namespacePrefix . '\VoyagerBaseController';
                $commanSlug = $dataType->slug;
                Route::get($commanSlug . '/order', [$namespacePrefix . $breadController, 'order'])->name($commanSlug . '.order');
                Route::post($commanSlug . '/action', [$namespacePrefix . $breadController, 'action'])->name($commanSlug . '.action');
                Route::post($commanSlug . '/order', [$namespacePrefix . $breadController, 'update_order'])->name($commanSlug . '.update_order');
                Route::get($commanSlug . '/{id}/restore', [$namespacePrefix . $breadController, 'restore'])->name($commanSlug . '.restore');
                Route::get($commanSlug . '/relation', [$namespacePrefix . $breadController, 'relation'])->name($commanSlug . '.relation');
                Route::post($commanSlug . '/remove', [$namespacePrefix . $breadController, 'remove_media'])->name($commanSlug . '.media.remove');

                Route::resource($commanSlug, $breadController)->parameters([$commanSlug => 'id']);
            }
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Custom routes hasn't been configured because: " . $e->getMessage(), 1);
        } catch (\Exception $e) {
            // do nothing, might just be because table not yet migrated.
        }

        // Menu Routes
        Route::group([
            'as'     => 'menus.',
            'prefix' => 'menus/{menu}',
            'controller' => $namespacePrefix . '\VoyagerMenuController',
        ], function () {
            Route::get('builder', 'builder')->name('builder');
            Route::post('order', 'order_item')->name('order_item');

            Route::group([
                'as'     => 'item.',
                'prefix' => 'item',
            ], function () {
                Route::delete('{id}', 'delete_menu')->name('destroy');
                Route::post('/', 'add_item')->name('add');
                Route::put('/', 'update_item')->name('update');
            });
        });

        // Settings
        Route::group([
            'as'     => 'settings.',
            'prefix' => 'settings',
            'controller' => $namespacePrefix . '\VoyagerSettingsController',
        ], function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/', 'update')->name('update');
            Route::delete('{id}', 'delete')->name('delete');
            Route::get('{id}/move_up', 'move_up')->name('move_up');
            Route::get('{id}/move_down', 'move_down')->name('move_down');
            Route::put('{id}/delete_value', 'delete_value')->name('delete_value');
        });

        // Admin Media
        Route::group([
            'as'     => 'media.',
            'prefix' => 'media',
            'controller' => $namespacePrefix . '\VoyagerMediaController',
        ], function () {
            Route::get('/', 'index')->name('index');
            Route::post('files', 'files')->name('files');
            Route::post('new_folder', 'new_folder')->name('new_folder');
            Route::post('delete_file_folder', 'delete')->name('delete');
            Route::post('move_file', 'move')->name('move');
            Route::post('rename_file', 'rename')->name('rename');
            Route::post('upload', 'upload')->name('upload');
            Route::post('crop', 'crop')->name('crop');
        });

        // BREAD Routes
        Route::group([
            'as'     => 'bread.',
            'prefix' => 'bread',
            'controller' => $namespacePrefix . '\VoyagerBreadController',
        ], function () {
            Route::get('/', 'index')->name('index');
            Route::get('{table}/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('{table}/edit', 'edit')->name('edit');
            Route::put('{id}', 'update')->name('update');
            Route::delete('{id}', 'destroy')->name('delete');
            Route::post('relationship', 'addRelationship')->name('relationship');
            Route::get('delete_relationship/{id}', 'deleteRelationship')->name('delete_relationship');
        });

        // Database Routes
        Route::resource('database', $namespacePrefix . '\VoyagerDatabaseController');

        // Compass Routes
        Route::group([
            'as'     => 'compass.',
            'prefix' => 'compass',
            'controller' => $namespacePrefix . '\VoyagerCompassController',
        ], function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'index')->name('post');
        });

        event(new RoutingAdminAfter());
    });

    //Asset Routes
    Route::get('voyager-assets', [$namespacePrefix . '\VoyagerController', 'assets'])->name('voyager_assets');

    event(new RoutingAfter());
});
