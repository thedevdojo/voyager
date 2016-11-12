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
        'default_role' => 'user',
        'namespace' => App\User::class,
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
        'prefix' => 'admin'
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

    /*
    |--------------------------------------------------------------------------
    | Menu Config
    |--------------------------------------------------------------------------
    |
    | Some settings for the menu
    |
    */

    'menu' => [
        'default' => 'default', // if no view is defined, this will be used
        'overwrites' => [ // overwrite some views with others
            'admin_menu' => 'voyager::menu.admin_menu',
        ],
    ],

];
