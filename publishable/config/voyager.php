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
        'namespace'                    => App\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission config
    |--------------------------------------------------------------------------
    |
    | Here you can specify conditions for accessing the admin panel
    |
    */

    'permission' => function () {
        $user = TCG\Voyager\Models\User::find(Auth::user()->id);
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    },

    /*
    |--------------------------------------------------------------------------
    | Model permission config
    |--------------------------------------------------------------------------
    |
    | Here you can specify conditions for accessing the specific models by action name
    |
    */

    'model_permission' => function ($action) {
        return true;
    },

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
