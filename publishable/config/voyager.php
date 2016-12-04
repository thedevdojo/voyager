<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User config
    |--------------------------------------------------------------------------
    |
    | Here you can specify voyager user configs
    |
    */

    'user' => [
        'add_default_role_on_register' => true,
        'default_role'                 => 'user',
        'admin_permission'             => 'browse_admin',
        'namespace'                    => App\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes config
    |--------------------------------------------------------------------------
    |
    | Here you can specify voyager route settings
    |
    */

    'routes' => [
        'prefix' => 'admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Controllers config
    |--------------------------------------------------------------------------
    |
    | Here you can specify voyager controller settings
    |
    */

    'controllers' => [
        'namespace' => 'TCG\\Voyager\\Http\\Controllers',
    ],

    /*
    |--------------------------------------------------------------------------
    | Path to the Voyager Assets
    |--------------------------------------------------------------------------
    |
    | Here you can specify the location of the voyager assets path
    |
    */

    'assets_path' => '/vendor/tcg/voyager/assets',

    /*
    |--------------------------------------------------------------------------
    | Storage Config
    |--------------------------------------------------------------------------
    |
    | Here you can specify attributes related to your application file system
    |
    */

    'storage' => [
        'subfolder' => 'public/', // include trailing slash, like 'my_folder/'
    ],

];
