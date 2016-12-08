Voyager Docs
=======

> Latest Version `0.10`
> 
## Welcome

Welcome to the Voyager Documentation. These docs will teach you how to install, configure, and use Voyager so that way you can create some kick ass stuff. 

Hm Hm (cough)... I mean... Arrgg! Ye young scallywag! What say we learn how to steer this ship!

# Install & Upgrade

## Install

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

If you did not go with the dummy user, you should make sure that your account has the needed role and permissions.
This can be done easy by running this command:

```bash
php artisan voyager:admin your@email.com
```

## Upgrade

The first step you should **always** do, is to take an entire backup of your application together with your database.

### Version 0.9 to 0.10

#### Estimated upgrade time: 20 minutes - 1 hour

> We attempt to document every possible breaking change.

#### Updating dependencies

Update your `tcg/voytager` dependency to `0.1.*` in your `composer.json` file. After this run a `composer update`.
> UNTIL RELEASE USE `dev-release/v0.10`.

#### Republish Voyager files

Some of the published files have been changed in the latest version, and you should therefor update them using the following command:
```bash
php artisan vendor:publish --provider="TCG\Voyager\VoyagerServiceProvider" --force
```

After this run `composer dumpautoload`.

#### Migrate database

Some changes have been made to the database, so to catch up run a `php artisan migrate`.

> NOTE: After this, please ensure that you in the `data_types` table have the `generate_permissions` column. If you do not have this, please add that as `TINYINT(1)` with default value of `0`.

#### Set permissions for data types

In your database, open up table `data_types` and update `generate_permissions` to `1` for the rows with the `name` that is in this list:
- menus
- pages
- roles
- users
- posts
- categories

> You may do this for others as well if you wish, but for everyone you do it for that are not listed above, open up `artisan tinker` and run `\TCG\Voyager\Models\Permission::generateFor(‘NAME’);` to generate permissions for them.

#### Add routes

Open your `routes/web.php` file and add the following:
```php
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
```

#### Update menus

To ensure that you have the latests menu items in your Voyager panel, run this command:
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

# Routing

## Voyager Routing

After running the voyager installer you will probably notice a few new routes that have been added to your `routes/web.php` file which look like the following:

```
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
```

This is where the voyager routes will be rendered. You can also choose to change the `admin` prefix to a more desired name.

When creating a new BREAD type and specifying a slug for that BREAD, you will then be able to visit that route via your admin via the following link:

```
URL/admin/slug-name
```

Say for instance that you added BREAD to a `products` table and you speicified the slug in that bread to be `products`. You will now be able to visit a URL in voyager located at:

```
URL/admin/products
```

> Note: you may not see a link to your newly created routes or BREAD inside your admin menu. To create a new link in your admin menu visit the documentation for the menu section.

# Database Tools

Voyager has some awesome database tools which allow you to Add/Edit/Delete or view current database tables. The other cool part of Voyager is that you can add BREAD or (Browse, Read, Edit, Add, & Delete) functionality to any of your tables.

## Database

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

- Check Box
- Drop Down
- Radio Button
- Image

Find out how to use these additional details below:

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
public function author_id(){
    return $this->belongsTo(User::class);
}
```

> Note: the method used for this relationship, must match the name of the row from the `pages` table. Which is why we used `author_id` as the method name to tie the relationship.

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

# Customization

## Overriding Views
You can override any of the BREAD views by creating a new folder in `resources/views/admin/slug-name` and *slug-name* will be the *slug* that you have assigned for that table. There are 2 files that you will include in each which will be:

 - browse.blade.php
 - edit-add.blade.php

By default an `admin/posts` view has been published to your `resources/views` folder. So those 2 view files will be located at `resources/views/admin/posts/browse.blade.php` and `resources/views/admin/posts/edit-add.blade.php`. 
