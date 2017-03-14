Voyager Docs
=======

Voyager Documentation for Version `0.10`

<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/v/stable.svg?format=flat" alt="Latest Stable Version"></a>

## Welcome

Welcome to the Voyager Documentation. These docs will teach you how to install, configure, and use Voyager so that way you can create some kick ass stuff. 

Hm Hm (cough)... I mean... Arrgg! Ye young scallywag! What say we learn how to steer this ship!

# Getting Started

## Installation

Voyager is super easy to install. After creating your new Laravel application you can include the Voyager package with the following command: 

```bash
composer require tcg/voyager
```

Next make sure to create a new database and add your database credentials to your .env file:

```
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Add the Voyager service provider to the `config/app.php` file in the `providers` array:

```php
'providers' => [
    // Laravel Framework Service Providers...
    //...
    
    // Package Service Providers
    TCG\Voyager\VoyagerServiceProvider::class,
    // ...
    
    // Application Service Providers
    // ...
],
```

Lastly, we can install voyager. You can do this either with or without dummy data.
The dummy data will include 1 admin account (if no users already exists), 1 demo page, 4 demo posts, 2 categories and 7 settings.

To install Voyager without dummy simply run

```bash
php artisan voyager:install
```

If you prefer installing it with dummy run

```bash
php artisan voyager:install --with-dummy
```

And we're all good to go! 

Start up a local development server with `php artisan serve` And, visit [http://localhost:8000/admin](http://localhost:8000/admin).

If you did go ahead with the dummy data, a user should have been created for you with the following login credentials:

>**email:** `admin@admin.com`   
>**password:** `password`

NOTE: Please note that a dummy user is **only** created if there are no current users in your database.

If you did not go with the dummy user, you may wish to assign admin priveleges to an existing user.
This can easily be done by running this command:

```bash
php artisan voyager:admin your@email.com
```

If you did not install the dummy data and you wish to create a new admin user you can pass the `--create` flag, like so:

```bash
php artisan voyager:admin your@email.com --create
```

And you will be prompted for the users name and password.

## Upgrading

The first step you should **always** do, is to take an entire backup of your application together with your database.

### Version 0.10

To update to the latest version inside of your composer.json file make sure to update the version of voyager inside the require declaration inside of your composer.json to:

```
"tcg/voyager": "0.10.*"
```

And then run composer update

Next, you may want to be sure that you have all the latest published assets. To re-publish the voyager assets you can run the following command:

```
php artisan vendor:publish --tag=voyager_assets --force
```

Next, there may be a few more settings added to the database schema that need updating in the latest version. To make sure you have the latest database schema, you'll need to visit:

> Currently only needed if you are upgrading and not currently on v0.10.6+

```
localhost:8000/admin/upgrade
```

After visiting the `upgrade` URL, you will be redirected to your dashboard. If a change has been made you'll see a notification specifying that the database schema has been updated. If you do not receive a message this update may not have included any database changes.

Lastly, you may wish to clear your view cache by running the following command:

```
php artisan view:clear
```

And now you'll be upgraded to the latest version.

### Version 0.9 to 0.10

#### Estimated upgrade time: 20 minutes - 1 hour

> We attempt to document every possible breaking change.

#### Updating dependencies

Update your `tcg/voyager` dependency to `0.10.*` in your `composer.json` file. After this run a `composer update`.

#### Republish Voyager files

Some of the published files have been changed in the latest version, and you should therefore update them using the following command:
```bash
php artisan vendor:publish --provider="TCG\Voyager\VoyagerServiceProvider" --force
```

After this run `composer dump-autoload`.

#### Migrate database

Some changes have been made to the database, so to catch up run a `php artisan migrate`.

> NOTE: After this, please ensure that in the `data_types` table you have the `generate_permissions` column. If you do not have this, please add that as `TINYINT(1)` with default value of `0`.
> Also open up the `pages` table and ensure that the following columns are `nullable`.
> * excerpt
> * body
> * meta_description
> * meta_keywords

#### Set permissions for data types

In your database, open up table `data_types` and update `generate_permissions` to `1` for the rows with the `name` that is in this list:
- menus
- pages
- roles
- users
- posts
- categories

> You may do this for others as well if you wish, but for everyone you do it for that are not listed above, open up `php artisan tinker` and run `\TCG\Voyager\Models\Permission::generateFor(‘NAME’);` to generate permissions for them.

#### Add routes

Open your `routes/web.php` file and add the following:
```php
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
```

#### Update menus

To ensure that you have the latest menu items in your Voyager panel, run this command:
```bash
php artisan db:seed --class=MenuItemsTableSeeder
```

#### Update roles and permissions

To ensure that you have the needed roles and permissions, run the following two commands:
```bash
php artisan db:seed --class=PermissionsTableSeeder
php artisan db:seed --class=PermissionRoleTableSeeder
```

#### Ensure admin access

Ensure that your account have full admin rights by running:
```bash
php artisan voyager:admin your@email.com
```

#### Cleanup

You may remove `Intervention\Image\ImageServiceProviderLaravel5` from your `providers` array in `config/app.php`.

#### In application changes

Models using the `VoyagerUser` trait does no longer have the `roles` relation. Instead we are using a single role now. So please update you application for usages of that relation.
Also the method `addRole` and `deleteRole` has been removed and replaced with a `setRole` method.

## Configuration

With the installation of voyager you will have a new configuration file located at `config/voyager.php`
In this file you can find various options to change the configuration of your voyager installation. Following is a more detailed description of each of the configuration sets:

#### User config

```
'user' => [
    'add_default_role_on_register' => true,
    'default_role'                 => 'user',
    'admin_permission'             => 'browse_admin',
    'namespace'                    => App\User::class,
],
```

**add_default_role_on_register**: Specify whether you would like to add the the default role to any new user that is created.

**default_role**: You can also specify what the **default_role** is of the user.

**admin_permission**: The permission needed to view the admin dashboard.

**namespace**: The namespace of your apps User Class.

#### Controller config

```
'controllers' => [
    'namespace' => 'TCG\\Voyager\\Http\\Controllers',
],
```

You can specify the default `controller` namespace of voyager so if you ever wish to override any of the core functionality of voyager you can do so by duplicating the Voyager Controllers and specifying the location of your custom controllers.

> If you do only wish to overwrite a single controller, you might consider adding the following piece of code in your `AppServiceProvider`'s `register`-method:
> ```
$this->app->bind(VoyagerBreadController::class, MyBreadController::class);
```

#### Asset config

```
'assets_path' => '/vendor/tcg/voyager/assets',
```

You may wish to specify a different asset path. If your site lives in a subfolder you may need to include that directory to the beginning of the path. This may also be used in case you wish to duplicate the published assets and customize your own.

> Note: When upgrading to new version of voyager the assets located in the /vendor/tcg/voyager/assets directory may need to be overwritten, so if you wish to customize any styles you will want to duplicate that directory and specify the new location of your asset_path.

#### Storage config

```
'storage' => [
    'subfolder' => 'public/', // include trailing slash, like 'my_folder/'
],
```

By default voyager is going to use the storage location and driver that is specified inside of your `config/filesystems.php` configuration. However, if you wish to have all of your assets inside of a specific subfolder in that storage location, you can specify it here.

# Core Concepts

## Routing

After running the voyager installer you will probably notice a few new routes that have been added to your `routes/web.php` file which look like the following:

```
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
```

This is where the voyager routes will be rendered. You can change the `admin` prefix if you desire, or set any other route configuration you need, such as `middleware` or `domain`.

When creating a new BREAD type and specifying a slug for that BREAD, you can then visit that route from the following link:

```
URL/admin/slug-name
```

As an example, if we have a `products` table and we specified the slug to be `products`. You will now be able to visit the following URL:

```
URL/admin/products
```

> Note: you may not see a link to your newly created routes or BREAD inside your admin menu. To create a new link in your admin menu visit the documentation for the menu section.

## Menus

With Voyager you can easily create menus for your application. In fact the Voyager admin is using the menu builder for the navigation you use on the left hand side.

You can view your current Menus by clicking on the *Tools->Menu Builder* button. You can add, edit, or delete any current menu. This means that you can create a new menu for the header, sidebar, or footer of your site. Create as many menus as you would like.

When you are ready to add menu items to your menu you can click on the builder button of the corresponding menu:

![Voyager Menus](/voyager/docs/0.10/images/menu-01.jpg)

This will take you to the Menu Builder where you can add, edit, and delete menu items.

![Voyager Menus](/voyager/docs/0.10/images/menu-02.jpg)

After creating and configuring your menu, you can easily implement that menu in your application. Say that we have a menu called `main`. Inside of any view file we could now output the menu by using the following code:

```
Menu::display('main');
```

This will output your menu in an unstyled unordered list. If you do use bootstrap to stylize your web app you can pass a second argument to the menu display method telling it that you want to stylize the menu with bootstrap styles like so:

```
Menu::display('main', 'bootstrap');
```

Taking it one more step further you can even specify your own view and stylize your menu however you would like. Say for instance that we had a file located at `resources/views/my_menu.blade.php`, which contained the following code:

```
<ul>
    @foreach($items as $menu_item)
        <li><a href="{{ $menu_item->url }}">{{ $menu_item->title }}</a></li>
    @endforeach
</ul>
```

Then anywhere you wanted to display your menu you can now call:

```
Menu::display('main', 'my_menu');
```

And your custom menu will now be output.

## Permissions

Voyager comes with roles and permissions out of the box. Each user can be asigned to a role and each role can have a set of permissions. You can check for a certain permission on the user object the following way:

```
$canVisitAdmin = $user->hasPermission('browse_admin');
$canVisitAdmin = auth()->user()->hasPermission('browse_admin');
```

The `hasPermission` method returns a boolean whether or not the user have a cetain permission. However you might wish to throw a forbidden exception if the user does not have a certain permission. This can be done using the `Voyager` facade:

```
Voyager::can('browse_admin');
```

This will either return true or throw a exception.

> Please note that this behavoir will change in `v0.11`, so that `Voyager::can` will return a boolean, but instead you can then use the `Voyager::canOrFail` method to throw a exception.

Out of the box there are some permissions you can use by default:
- `browse_admin`: Whether or not the user may browse the Voyager admin panel.
- `browse_database`: Whether or not the user may browse the Voyager database menu section.
- `browse_media`: Whether or not the user may browse the Voyager media section.
- `browse_settings`: Whether or not the user may browse the Voyager settings section.
- `browse_menu`: Whether or not the user may browse the Voyager menu section.

Then whenever you create some BREAD for a table, you can define to `Generate permissions`. This will create the `browse`, `read`, `edit`, `add` and `delete` permission. Example for the `menus` BREAD, those permission keys will be `browse_menus`, `read_menus`, `edit_menus`, `add_menus` and `delete_menus`.

> Please note that if a menu item goes to any kind of BREAD, then it will check for the `browse` permission, for example for the `Menu` BREAD menu item, it will check for the `browse_menus` permission. If the user does not have the required permission, that menu item will be hidden.

## Database Tools

Voyager has some awesome database tools which allow you to Add/Edit/Delete or view current database tables. The other cool part of Voyager is that you can add BREAD or (Browse, Read, Edit, Add, & Delete) functionality to any of your tables.

Inside of your admin panel you can visit Tools->Database and you'll be able to view all your current tables in your database. You may also click on 'Create a New Table' to create a new table in your database.

If you click the table name you can view the current schema. Additionally you can click on the View, Edit, or Delete buttons to perform that action for that table.

You may also choose to Add BREAD (Browse, Read, Edit, Add, & Delete) for any of your database tables. Once a table already has BREAD you may choose to edit the current BREAD or Delete the BREAD for that table.

## BREAD

When adding or editing the current BREAD for a database table you can select where in your views you want to see each of those fields:

* BROWSE (field will show up when you browse the current data)
* READ (field will show when you click to view the current data)
* EDIT (field will be visible and allow you to edit the data)
* ADD (field will be visible when you choose to create a new data type)
* DELETE (doesn't pertain to delete so this can be checked or unchecked)

You may also choose to specify what form type you want to use for each field. This can be a TextBox, TextArea, Checkbox, Image, and many other types of form elements.

Each field also has additional details or options that can be included. These types are checkbox, dropdown, radio button, and image. Learn more about these options below:

### Additional Field Options

When Editing Your Browse, Read, Edit, Add, and Delete Rows you have a select box that allows you to include additional details or options for your datatype. This textarea accepts JSON and it applies to the following types of inputs:

- Text (Text Box, Text Area, Rich Textbox and Hidden)
- Check Box
- Drop Down
- Radio Button
- Image
- Date

Find out how to use these additional details below:

#### Text (Text Box, Text Area, Rich Textbox and Hidden)

```js
{
    "default" : "Default text"
}
```

Text Box, Text Area, Rich Textbox and Hidden are all kind of texts inputs. In the JSON above you can specify the `default` value of the input.

#### Check Box
```js
{
    "on" : "On Text",
    "off" : "Off Text",
    "checked" : "true"
}
```

In Voyager a Check Box is converted into a toggle switch, and as you can see above the `on` key will contain the value when the toggle switch is on, and the `off` will contain the value that is set when the switch is off. If `checked` is set to *true* the checkbox will be toggle on; otherwise by default it will be off.

#### Drop Down
```js
{
    "default" : "option1",
    "options" : {
        "option1": "Option 1 Text",
        "option2": "Option 2 Text"
    }
}
```

When specifying that an input type should be a dropdown you will need to specify the values of that dropdown. In the JSON above you can specify the `default` value of the dropdown if it does not have a value. Additionally, in the `options` object you will specify the *value* of the option on the **left** and the *text* to be displayed on the **right**.


#### Radio Button
```js
{
    "default" : "radio1",
    "options" : {
        "radio1": "Radio Button 1 Text",
        "radio2": "Radio Button 2 Text"
    }
}
```

The Radio button is exactly the same as the dropdown. You can specify a `default` if one has not been set and in the `options` object you will specify the *value* of the option on the **left** and the *text* to be displayed on the **right**.

#### Image
```js
{
    "resize": {
        "width": "1000",
        "height": "null"
    },
    "quality" : "70%",
    "upsize" : true,
    "thumbnails": [
        {
            "name": "medium",
            "scale": "50%"
        },
        {
            "name": "small",
            "scale": "25%"
        },
        {
            "name": "cropped",
            "crop": {
                "width": "300",
                "height": "250"
            }
        }
    ]
}
```

The image input has many options. By default if you do not specify any options no problem... Your image will still be uploaded. But, if you want to resize an image, set the quality of the image, or specify thumbnails for the uploaded image you will need to specify those details.

**resize**
If you want to specify a size you will need to include that in the `resize` object. If you set either **height** or **width** to null it will keep the aspect ratio based on the width or height that is set. So, for the example above the `width` is set to `1000` pixels and since the `height` is set to `null` it will resize the image width to 1000 pixels and resize the height based on the current aspect ratio.

**quality**
If you wish to compress the image with a percentage quality you can specify that percentage in the `quality` key. Typically between 70 and 100% there is little notice of image quality, but the image size may be dramatically lower.

**upsize**
This is only valid if you have set your image to be resized. If you specify your image to resized to 1000 pixels and the image is smaller than 1000 pixels by default it will not upsize that image to the 1000 pixels; however, if you set `upsize` to true. It will upsize all images to your specified resize values.

**thumbnails**
Thumbnails takes an array of objects. Each object is a new thumbnail that is created. Each object contains 2 values, the `name` and `scale` percentage. The `name` will be attached to your thumbnail image (as an example say the image you uploaded was ABC.jpg a thumbnail with the `name` of `medium` would now be created at ABC-medium.jpg). The `scale` is the percentage amount you want that thumbnail to scale. This value will be a percentage of the *resize* width and height if specified.

#### Date

```js
{
    "format" : "Y-m-d"
}
```

The date input field is where you can input a date. In the JSON above you can specify the `format` value of the output of the date. It allows you to display a formatted `date` in browse and read views, using Carbon's `formatLocalized()` method

### Description

All types can include a description in order to help your future self or other users using your Voyager admin panel to understand exactly what a specific BREAD input field is for, this can be defined in the `Optional Details` JSON input field:

```
{
    "description": "A helpful description text here for your future self."
}
```

### Validation

Inside of the *Optional Details* section for each row in your BREAD you can also specify validation rules with some simple JSON. Here is an example of how to add a validation rule or *required* and *max length of 12*

```
{
    "validation": {
        "rule": "required|max:12"
    }
}
```

Additionally, you may wish to add some custom error messages which can be accomplished like so:

```
{
    "validation": {
        "rule": "required|max:12",
        "messages": {
            "required": "This :attribute field is a must.",
            "max": "This :attribute field maximum :max."
        }
    }
}
```

Since `v0.10.13` you can do the `required` and `max:12` rule the following way:
```
{
    "validation": {
        "rules": [
            "required",
            "max:12"
        ]
    }
}
```

### Generating Slugs

Using the bread builder you may wish to automatically generate slugs of a cetain input. Lets say you have some posts, which have a title and a slug. If you want to automatically generate the slug from the title attribute, you may include the following *Optional Details*:

```
{
    "slugify": {
        "origin": "title", 
        "forceUpdate": true
    }
}
```

This will automatically generate the slug from the input of the `title` field. If a slug does already exists, it will only be updated if `forceUpdate` is set enabled, by default this is dissabled.

### Relationships

Using the bread builder additional options you can add relationships to rows. There are 2 input types that will allow you to implement a relationship with another table.

- Dropdown
- Multiple Select

#### Dropdown

A dropdown can create a `belongsTo` relationship from the current DataType to another table. Let's say for instance that we had an `author_id` row in the a `pages` table and we wanted to correspond that with an `id` from a `users` table. 

Simple enough, in the `pages` BREAD we could choose a *Select Dropdown* as the input type of the `author_id` row and include the following *Optional Details*:

```
{
    "relationship": {
        "key": "id",
        "label": "name"
    }
}
```

The **key** above will be the row that we want to use as the value in the select dropddown and the **label** will be the row that we want to display in the dropdown.

Finally, we'll need to create a relationship in the `Page` class. This would look like the following:

```
public function authorId(){
    return $this->belongsTo(User::class);
}
```

> Note: the method used for this relationship, must match the camelCase version of the row from the `pages` table. Which is why we used `authorId` as the method name to tie the relationship.

You can optionally add a new page_slug property to the relationships object in the BREAD details in order to display proper links to relationship records. i.e.:

```
{
    "relationship": {
        "key": "id",
        "label": "name",
        "page_slug": "admin/users"
    }
}
```

> If no page_slug is provided, then the real "text" (`relationship.label`) is still being "resolved", but no anchor link is created.

And that's how we can perform a One-to-One relationship. Next, you'll see how to create a Many-to-Many relationship.

#### Multiple Select

Using a multi select dropdown we can create a `belongsToMany` relationship. This adds a Many-to-Many relationship between 2 tables using a pivot table.

As an example we will say that we have a `categories` table, `pages` table, and the pivot table `category_page`.

Simply enough, inside of the `pages` BREAD we would choose a *Multiple Select* as the input type for a `categories` row (this row can be any type) and include the following *Optional Details* as we did above:

```
{
    "relationship": {
        "key": "id",
        "label": "name"
    }
}
```

Now, we'll need to create the relationship inside of the `Page` class like so:

```
public function categories(){
    return $this->belongsToMany(Category::class);
}
```

> Note: if you are using a pivot table that has a different table name then the typical convention, you can specify the name of that pivot table like so:

```
public function categories(){
    return $this->belongsToMany(Category::class, 'page_categories');
}
```

Now, when you have save the results from your Many-to-Many relationship the ID's of each selected value will be synced and added to your pivot table.

> Please note that this can also be configured as a normal `Select Dropdown` in case you need a simple multiple selection with fixed `options` instead of `relationship`.

You can optionally add a new page_slug property to the relationships object in the BREAD details in order to display proper links to relationship records. i.e.:

```
{
    "relationship": {
        "key": "id",
        "label": "name",
        "page_slug": "admin/users"
    }
}
```

> If no page_slug is provided, then the real "text" (`relationship.label`) is still being "resolved", but no anchor link is created.

### NULL values

You might want to save an input field into the database as a `null` value instead of an empty string.

Simply enough, inside the BREAD you can include the following *Optional Details* for the field:

```
{
    "null": ""
}
```

This will turn an empty string into a `null` value. However you might want to be able to add both an empty string and a `null` value to the database for that field. However you have to choose a replacement for the `null` value, but it can be anything you wish. For example,  if you want a field to change a string (ex. `Nothing`) into a `null` value you could include the following *Optional Details* for that field:

```
{
    "null": "Nothing"
}
```

Now entering `Nothing` into the field will end up as a `null` value in the database.

# Customization

## Overriding Views
You can override any of the BREAD views by creating a new folder in `resources/views/vendor/voyager/slug-name` and *slug-name* will be the *slug* that you have assigned for that table. There are 2 files that you will include in each which will be:

 - browse.blade.php
 - edit-add.blade.php

By default a couple `posts` views have been published to your `resources/views/vendor/voyager` folder. So those 2 view files will be located at `resources/views/vendor/voyager/posts/browse.blade.php` and `resources/views/vendor/voyager/posts/edit-add.blade.php`. 

## Using Custom HTTP Controllers
You can use your own Controller by extending Voyager's Controllers. To do it, first define your controller Namespace at `config/voyager.php` :
```
/*
    |--------------------------------------------------------------------------
    | Controllers config
    |--------------------------------------------------------------------------
    |
    | Here you can specify voyager controller settings
    |
    */

    'controllers' => [
        'namespace' => 'App\\Http\\Controllers\\Voyager',
    ],
```

then run `php artisan voyager:controllers`, voyager will now use the child controllers which will be created at `App/Http/Controllers/Voyager`

## Overriding Routes
You can override any Voyager routes by writing the routes you want to overwrite below `Voyager::routes()`. For example if you want to override your post LoginController:

```
Route::group(['prefix' => 'admin'], function () {
   Voyager::routes();

   // Your overwrites here
   Route::post('login', ['uses' => 'MyAuthController@postLogin', 'as' => 'postlogin']);
});
```
