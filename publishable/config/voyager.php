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
        'default_avatar'               => 'users/default.png',
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
    | Models config
    |--------------------------------------------------------------------------
    |
    | Here you can specify default model namespace when creating BREAD.
    | Must include trailing backslashes. If not defined the default application
    | namespace will be used.
    |
    */

    'models' => [
        //'namespace' => 'App\\',
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
        'disk' => 'public',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Config
    |--------------------------------------------------------------------------
    |
    | Here you can specify voyager database settings
    |
    */

    'database' => [
        'tables' => [
            'hidden' => ['migrations', 'data_rows', 'data_types', 'menu_items', 'password_resets', 'permission_role', 'permissions', 'settings'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Multilingual configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specify if you want Voyager to ship with support for
    | multilingual and what locales are enabled.
    |
    */

    'multilingual' => [
        /*
         * Set whether or not the multilingual is supported by the BREAD input.
         */
        'bread' => false,

        /*
         * Select default language
         */
        'default' => 'en',

        /*
         * Select languages that are supported.
         */
        'locales' => [
            'en',
            //'pt',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets Config
    |--------------------------------------------------------------------------
    |
    | Here you can specify voyager administration settings
    |
    */

    'widgets' => [
        [
            'name'  => 'User',
            'icon'  => 'voyager-group',
            'model' => TCG\Voyager\Models\User::class,
            'url'   => 'admin/users',
            'image' => '/images/widget-backgrounds/02.png',
        ],
        [
            'name'  => 'Post',
            'icon'  => 'voyager-news',
            'model' => TCG\Voyager\Models\Post::class,
            'url'   => 'admin/posts',
            'image' => '/images/widget-backgrounds/03.png',
        ],
        [
            'name'  => 'Page',
            'icon'  => 'voyager-file-text',
            'model' => TCG\Voyager\Models\Page::class,
            'url'   => 'admin/pages',
            'image' => '/images/widget-backgrounds/04.png',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard config
    |--------------------------------------------------------------------------
    |
    | Here you can modify some aspects of your dashboard
    |
    */

    'dashboard' => [
        // Add custom list items to navbar's dropdown
        'navbar_items' => [
            'Profile' => [
                'route'         => 'voyager.profile',
                'classes'       => 'class-full-of-rum',
                'icon_class'    => 'voyager-person',
            ],
            'Home' => [
                'route'         => '/',
                'icon_class'    => 'voyager-home',
                'target_blank'  => true,
            ],
            'Logout' => [
                'route'      => 'voyager.logout',
                'icon_class' => 'voyager-power',
            ],
        ],
        'data_tables' => [
            'responsive' => true, // Use responsive extension for jQuery dataTables that are not server-side paginated
        ],
        'widgets' => [
            'TCG\\Voyager\\Widgets\\UserDimmer',
            'TCG\\Voyager\\Widgets\\PostDimmer',
            'TCG\\Voyager\\Widgets\\PageDimmer',
        ],
    ],

    'login' => [
        'gradient_a' => '#ffffff',
        'gradient_b' => '#ffffff',
    ],

    'primary_color' => '#22A7F0',
    /*
 |--------------------------------------------------------------------------
 | Views config
 |--------------------------------------------------------------------------
 |
 | Here you can modify where the application views point to
 |
 */

    'views' => [
        'bread' => [
            'browse'        => env('VOYAGER').'bread.browse',
            'edit_read'          => env('VOYAGER').'bread.edit-add',
            'read'          => env('VOYAGER').'bread.read',
            'partials'      => [
                'relationship'    => env('VOYAGER').'bread.partials.relationship-edit-add',
            ],
        ],
        'dashboard' => [
            'navbar'         => env('VOYAGER').'dashboard.navbar',
            'sidebar'        => env('VOYAGER').'dashboard.sidebar',
        ],
        'formfields' => [
            'checkbox'          => env('VOYAGER').'formfields.checkbox',
            'code_editor'       => env('VOYAGER').'formfields.code_editor',
            'date'              => env('VOYAGER').'formfields.date',
            'file'              => env('VOYAGER').'formfields.file',
            'hidden'            => env('VOYAGER').'formfields.hidden',
            'image'             => env('VOYAGER').'formfields.image',
            'timestamp'         => env('VOYAGER').'formfields.timestamp',
            'text'              => env('VOYAGER').'formfields.text',
            'number'            => env('VOYAGER').'formfields.number',
            'password'          => env('VOYAGER').'formfields.password',
            'radio'             => env('VOYAGER').'formfields.radio_btn',
            'rich_text'         => env('VOYAGER').'formfields.rich_text_box',
            'select_dropdown'   => env('VOYAGER').'formfields.select_dropdown',
            'select_multiple'   => env('VOYAGER').'formfields.select_multiple',
            'multiple_images'   => env('VOYAGER').'formfields.multiple_images',
            'text_area'         => env('VOYAGER').'formfields.text_area',
        ],
        'media' => [
                'index'         => env('VOYAGER').'media.index',
        ],
        'menu' => [
            'admin'           => env('VOYAGER').'menu.admin',
            'admin_menu'      => env('VOYAGER').'menu.admin_menu',
            'bootstrap'       => env('VOYAGER').'menu.bootstrap',
            'default'         => env('VOYAGER').'menu.default',
        ],
        'menus' => [
            'browse'          => env('VOYAGER').'menus.admin',
            'builder'         => env('VOYAGER').'menus.builder',
            'partial'         => [
                'notice'      => env('VOYAGER').'menus.partial.notice',
            ],
        ],
        'multilingual' => [
                'bread'         => env('VOYAGER').'multilingual.input-hidden-bread',
                'browse'        => env('VOYAGER').'multilingual.input-hidden-bread-browse',
                'menu'          => env('VOYAGER').'multilingual.input-hidden-menu',
                'selector'      => env('VOYAGER').'multilingual.language-selector',
        ],
        'partials' => [
                'footer'         => env('VOYAGER').'partials.app-footer',
        ],
        'posts' => [
                'browse'         => env('VOYAGER').'posts.browse',
                'edit_add'       => env('VOYAGER').'posts.edit-add',
        ],
        'roles' => [
                'edit_add'       => env('VOYAGER').'roles.edit-add',
        ],
        'settings' => [
                'index'          => env('VOYAGER').'settings.index',
        ],
        'tools' => [
                'database'      => [
                    'edit_add'  => env('VOYAGER').'tools.database.edit-add',
                    'bread'     => env('VOYAGER').'tools.database.edit-add-bread',
                    'index'     => env('VOYAGER').'tools.database.index',
                    'vue'       => [
                        'database_column'            => env('VOYAGER').'tools.database.vue-components.database-column',
                        'database_column_default'    => env('VOYAGER').'tools.database.vue-components.database-column-default',
                        'database_table_editor'      => env('VOYAGER').'tools.database.vue-components.database-table-editor',
                        'database_table_helper'      => env('VOYAGER').'tools.database.vue-components.database-table-helper-buttons',
                        'database_types'             => env('VOYAGER').'tools.database.vue-components.database-types',
                    ],
                ],
        ],
        'users' => [
                'browse'        => env('VOYAGER').'users.profile',
                'edit_add'      => env('VOYAGER').'users.edit-add',
                'read'          => env('VOYAGER').'users.read',
        ],
        'alerts'    => env('VOYAGER').'alerts',
        'dimmer'    => env('VOYAGER').'dimmer',
        'dimmers'   => env('VOYAGER').'dimmers',
        'index'     => env('VOYAGER').'index',
        'login'     => env('VOYAGER').'login',
        'master'    => env('VOYAGER').'master',
        'profile'   => env('VOYAGER').'profile',
        'fragment'  => env('VOYAGER').'browse'
  ]
];
