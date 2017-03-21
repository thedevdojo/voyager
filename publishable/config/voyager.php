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
            'browse'        => 'bread.browse',
            'edit'          => 'bread.edit-add',
            'read'          => 'bread.read',
            'partials'      => [
                'relationship'    => 'bread.partials.relationship-edit-add',
            ],
        ],
        'dashboard' => [
            'navbar'         => 'dashboard.navbar',
            'sidebar'        => 'dashboard.sidebar',
        ],
        'formfields' => [
            'checkbox'          => 'formfields.profile',
            'code_editor'       => 'formfields.code_editor',
            'date'              => 'formfields.date',
            'file'              => 'formfields.file',
            'hidden'            => 'formfields.hidden',
            'image'             => 'formfields.image',
            'timestamp'         => 'formfields.timestamp',
            'text'              => 'formfields.text',
            'number'            => 'formfields.number',
            'password'          => 'formfields.password',
            'radio'             => 'formfields.radio_btn',
            'rich_text'         => 'formfields.rich_text_box',
            'select_dropdown'   => 'formfields.select_dropdown',
            'select_multiple'   => 'formfields.select_multiple',
            'multiple_images'   => 'formfields.multiple_images',
            'text_area'         => 'formfields.text_area',
        ],
        'media' => [
                'index'         => 'media.index',
        ],
        'menu' => [
            'admin'           => 'menu.admin',
            'admin_menu'      => 'menu.admin_menu',
            'bootstrap'       => 'menu.bootstrap',
            'default'         => 'menu.default',
        ],
        'menus' => [
            'browse'          => 'menus.admin',
            'builder'         => 'menus.builder',
            'partial'         => [
                'notice'      => 'menus.partial.notice',
            ],
        ],
        'multilingual' => [
                'bread'         => 'multilingual.input-hidden-bread',
                'browse'        => 'multilingual.input-hidden-bread-browse',
                'menu'          => 'multilingual.input-hidden-menu',
                'selector'      => 'multilingual.language-selector',
        ],
        'partials' => [
                'footer'         => 'partials.app-footer',
        ],
        'posts' => [
                'browse'         => 'posts.browse',
                'edit_add'       => 'posts.edit-add',
        ],
        'roles' => [
                'edit_add'       => 'roles.edit-add',
        ],
        'settings' => [
                'index'          => 'settings.index',
        ],
        'tools' => [
                'database'      => [
                    'edit_add'  => 'tools.database.edit-add',
                    'bread'     => 'tools.database.edit-add-bread',
                    'index'     => 'tools.database.index',
                    'vue'       => [
                        'database_column'            => 'tools.database.vue-components.database-column',
                        'database_column_default'    => 'tools.database.vue-components.database-column-default',
                        'database_table_editor'      => 'tools.database.vue-components.database-table-editor',
                        'database_table_helper'      => 'tools.database.vue-components.database-table-helper-buttons',
                        'database_types'             => 'tools.database.vue-components.database-types',
                    ],
                ],
        ],
        'users' => [
                'browse'        => 'users.profile',
                'edit_add'      => 'users.edit-add',
                'read'          => 'users.read',
        ],
        'alerts' => 'alerts',
        'dimmer' => 'dimmer',
        'dimmers' => 'dimmers',
        'index' => 'index',
        'login' => 'login',
        'master' => 'master',
        'profile' => 'profile',

  ]
];
