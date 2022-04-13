# Configurations

With the installation of Voyager you will find a new configuration file located at `config/voyager.php`.  
In this file you can find various options to change the configuration of your Voyager installation.

{% hint style="info" %}
If you cache your configuration files please make sure to run `php artisan config:clear` after you changed something.
{% endhint %}

Below we will take a deep dive into the configuration file and give a detailed description of each configuration set.

## Users

```php
<?php

'user' => [
    'add_default_role_on_register' => true,
    'default_role'                 => 'user',
    'admin_permission'             => 'browse_admin',
    'namespace'                    => App\User::class,
    'redirect'                     => '/admin'
],
```

**add\_default\_role\_on\_register:** Specify whether you would like to add the default role to any new user that is created.  
**default\_role:** You can also specify what the **default\_role** is of the user.  
**admin\_permission:** The permission needed to view the admin dashboard.  
**namespace:** The namespace of your apps User Class.  
**redirect:** Redirect path after the user logged in.

## Controller

```php
<?php

'controllers' => [
    'namespace' => 'TCG\\Voyager\\Http\\Controllers',
],
```

You can specify the default `controller` namespace of Voyager. If you ever wish to override any of the core functionality of Voyager you can do so by duplicating the Voyager controllers and specifying the location of your custom controllers.

{% hint style="info" %}
**Overwrite a single controller**  
If you only want to overwrite a single controller, you might consider adding the following piece of code to your `AppServiceProvider` class in the `register` method.  
`$this->app->bind(VoyagerBreadController::class, MyBreadController::class);`
{% endhint %}

## Model

```php
<?php

'models' => [
    //'namespace' => 'App\\',
],
```

You can specify the namespace or location of your models. This is used when creating the Models from the database section of Voyager. If not defined the default application namespace will be used.

## Assets

```php
<?php

'assets_path' => '/vendor/tcg/voyager/assets',
```

You may wish to specify a different asset path. If your site lives in a subfolder you may need to include that directory to the beginning of the path. This may also be used in case you wish to duplicate the published assets and customize your own.

{% hint style="info" %}
When upgrading to new version of voyager the assets located in the `/vendor/tcg/voyager/assets` directory may need to be overwritten, so if you wish to customize any styles you will want to duplicate that directory and specify the new location of your asset\_path.
{% endhint %}

## Storage

```php
<?php

'storage' => [
    'disk' => 'public',
],
```

By default Voyager is going to use the `public` local storage. You can additionally use any driver inside of your `config/filesystems.php`. This means you can use S3, Google Cloud Storage, or any other file storage system you would like.

## Database

```php
<?php

'database' => [
    'tables' => [
        'hidden' => ['migrations', 'data_rows', 'data_types', 'menu_items', 'password_resets', 'permission_role', 'personal_access_tokens', 'settings'],
    ],
    'autoload_migrations' => true,
],
```

You may wish to hide some database tables in the Voyager database section. In the database config you can choose which tables would like to hide.  
`autoload_migrations` allows you to exclude Voyagers migration-files from loading when running `php artisan migrate`.

## Multilingual

```php
<?php

'multilingual' => [
    'enabled' => false,
    'default' => 'en',
    'locales' => [
        'en',
        //'pt',
    ],
],
```

You can specify whether or not you want to **enable** mutliple languages. You can then specify your **default** language and all the support languages \(**locales**\)

Read more about multilanguage [here](../core-concepts/multilanguage.md).

## Dashboard

```php
<?php

'dashboard' => [
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
    'widgets' => [
        'TCG\\Voyager\\Widgets\\UserDimmer',
        'TCG\\Voyager\\Widgets\\PostDimmer',
        'TCG\\Voyager\\Widgets\\PageDimmer',
    ],
],
```

In the dashboard config you can add **navbar\_items**, make the **data\_tables** responsive, and manage your dashboard **widgets**.

**navbar\_items** Include a new route in the main user navbar dropdown by including a 'route', 'icon\_class', and 'target\_blank'.

**data\_tables** If you set 'responsive' to true the datatables will be responsive.

**widgets** Here you can manage the widgets that live on your dashboard. You can take a look at an example widget class by viewing the current widgets inside of `tcg/voyager/src/Widgets`.

## Primary color

```php
<?php

'primary_color' => '#22A7F0',
```

The default primary color for the Voyager admin dashboard is a light blue color. You can change that primary color by changing the value of this config.

## Show developer tips

```php
<?php

'show_dev_tips' => true,
```

In the Voyager admin there are developer tips or notifications that will show you how to reference certain values from Voyager. You can choose to hide these notifications by setting this configuration value to false.

## Additional stylesheets

```php
<?php

'additional_css' => [
    //'css/custom.css',
],
```

You can add your own custom stylesheets that will be included in the Voyager admin dashboard. This means you could technically create a whole new theme for Voyager by adding your own custom stylesheet.

Read more [here](../customization/additional-css-js.md).

{% hint style="info" %}
The path will be passed to Laravels [asset](https://laravel.com/docs/helpers#method-asset) function.
{% endhint %}

## Additional Javascript

```php
<?php

'additional_js' => [
    //'js/custom.js',
],
```

The same goes for this configuration. You can add your own javascript that will be executed in the Voyager admin dashboard. Add as many javascript files as needed.

Read more [here](../customization/additional-css-js.md).

## Google Maps

```php
<?php

'googlemaps' => [
    'key'    => env('GOOGLE_MAPS_KEY', ''),
    'center' => [
        'lat' => env('GOOGLE_MAPS_DEFAULT_CENTER_LAT', '32.715738'),
        'lng' => env('GOOGLE_MAPS_DEFAULT_CENTER_LNG', '-117.161084'),
    ],
    'zoom' => env('GOOGLE_MAPS_DEFAULT_ZOOM', 11),
],
```

There is a new data type called **coordinates** that allow you to add a google map as a datatype. The user can then drag and drop a pin in the Google Maps to save a longitude and latitude value in the database.

In this config you can set the default Google Maps Keys and center location. This can also be added to your .env file.

## Allowed Mimetypes

To allow/disallow mimetypes to be uploaded through the media-manager you can define an array `allowed_mimetypes`:

```php
<?php

'allowed_mimetypes' => [
     'image/jpeg',
     'image/png',
     'image/gif',
     'image/bmp',
     'video/mp4',
],
```

The user can only upload files with the given mimetypes. If you want to allow all types to be uploaded you can use the following:

```php
<?php

'allowed_mimetypes' => '*',
```

