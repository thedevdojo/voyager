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
        'admin_permission'             => 'visit_admin',
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

    /*
    |--------------------------------------------------------------------------
    | Models config
    |--------------------------------------------------------------------------
    |
    | Here you can specify models related to your Voyager system
    |
    */

    'models' => [
        'user' => \TCG\Voyager\Models\VoyagerUser::class,
        'menu' => \TCG\Voyager\Models\VoyagerMenu::class,
        'menu_item' => \TCG\Voyager\Models\VoyagerMenuItem::class,
        'category' => \TCG\Voyager\Models\VoyagerCategory::class,
        'page' => \TCG\Voyager\Models\VoyagerPage::class,
        'post' => \TCG\Voyager\Models\VoyagerPost::class,
        'data_row' => \TCG\Voyager\Models\VoyagerDataRow::class,
        'data_type' => \TCG\Voyager\Models\VoyagerDataType::class,
        'permission' => \TCG\Voyager\Models\VoyagerPermission::class,
        'role' => \TCG\Voyager\Models\VoyagerRole::class,
        'setting' => \TCG\Voyager\Models\VoyagerSetting::class,
    ],

];
