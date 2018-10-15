# Configurations

With the installation of voyager you will have a new configuration file located at `config/voyager.php` In this file you can find various options to change the configuration of your voyager installation. Following is a more detailed description of each of the configuration sets:

**User Config**

```php
'user' => [
    'add_default_role_on_register' => true,
    'default_role'                 => 'user',
    'admin_permission'             => 'browse_admin',
    'namespace'                    => App\User::class,
],
```

**add\_default\_role\_on\_register**: Specify whether you would like to add the default role to any new user that is created.

**default\_role**: You can also specify what the **default\_role** is of the user.

**admin\_permission**: The permission needed to view the admin dashboard.

**namespace**: The namespace of your apps User Class.

**Controller Config**

```php
'controllers' => [
    'namespace' => 'TCG\\Voyager\\Http\\Controllers',
],
```

You can specify the default `controller` namespace of voyager so if you ever wish to override any of the core functionality of voyager you can do so by duplicating the Voyager Controllers and specifying the location of your custom controllers.

> If you do only wish to overwrite a single controller, you might consider adding the following piece of code in your `AppServiceProvider`'s `register`-method:
>
> ```php
> $this->app->bind(VoyagerBreadController::class, MyBreadController::class);
> ```

**Model Config**

```php
'models' => [
    //'namespace' => 'App\\',
],
```

You can specify the namespace or location of your models. This is used when creating the Models from the database section of Voyager. If not defined the default application namespace will be used.

**Asset Config**

```php
'assets_path' => '/vendor/tcg/voyager/assets',
```

You may wish to specify a different asset path. If your site lives in a subfolder you may need to include that directory to the beginning of the path. This may also be used in case you wish to duplicate the published assets and customize your own.

> Note: When upgrading to new version of voyager the assets located in the /vendor/tcg/voyager/assets directory may need to be overwritten, so if you wish to customize any styles you will want to duplicate that directory and specify the new location of your asset\_path.

**Storage Config**

```php
'storage' => [
    'disk' => 'public',
],
```

By default voyager is going to use the `public` local storage. You can additionally use any driver inside of your `config/filesystems.php`. This means you can use s3, google cloud storage, or any other file storage system you would like.

**Database Config**

```php
'database' => [
    'tables' => [
        'hidden' => ['migrations', 'data_rows', 'data_types', 'menu_items', 'password_resets', 'permission_role', 'settings'],
    ],
],
```

You may wish to hide some database tables in the Voyager database section. In the database config you can choose which tables would like to hide.

**Prefix Config**

```php
'prefix' => 'admin',
```

In this config you can specify an alternate prefix for visiting voyager. Instead of visiting `/admin` perhaps you want to visit `/backend` to visit the voyager admin.

**Multilingual Config**

Thanks to all the contributors to voyager we now have multilingual support.

```php
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

**Dashboard Config**

```php
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

**Primary Color Config**

```php
'primary_color' => '#22A7F0',
```

The default primary color for the Voyager admin dashboard is a light blue color. You can change that primary color by changing the value of this config.

**Show Dev Tips Config**

```text
'show_dev_tips' => true,
```

In the Voyager admin there are dev tips or notifications that will show you how to reference certain values from voyager. You can choose to hide these notifications by setting this configuration to false.

**Additional CSS Config**

```php
'additional_css' => [
    //'css/custom.css',
],
```

This is super cool, you can add your own custom stylesheets that will be included in the Voyager Admin dashboard. This means you could technically create a whole new theme for Voyager by adding your own custom stylesheet.

**Additional JS Config**

```php
'additional_js' => [
    //'js/custom.js',
],
```

The same goes for this configuration. You can add your own javascript that will be executed in the Voyager admin dashboard. Add as many js files as needed.

**Google Maps Config**

```php
'googlemaps' => [
    'key'    => env('GOOGLE_MAPS_KEY', ''),
    'center' => [
        'lat' => env('GOOGLE_MAPS_DEFAULT_CENTER_LAT', '32.715738'),
        'lng' => env('GOOGLE_MAPS_DEFAULT_CENTER_LNG', '-117.161084'),
    ],
    'zoom' => env('GOOGLE_MAPS_DEFAULT_ZOOM', 11),
],
```

There is a new data type called **coordinates** that allow you to add a google map as a datatype. The user can then drag and drop a pin in the google map to save a longitude and latitude value in the database.

In this config you can set the default Google Maps Keys and center location. This call also be added to your .env file.

