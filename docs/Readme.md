Voyager Docs
=======

>> THESE DOCS ARE CURRENTLY STILL IN PROGRESS

![](../images/helm.png)
Welcome to the Voyager Documentation. These docs will teach you how to install, configure, and use Voyager so that way you can create some kick ass stuff. 

Hm Hm (cough)... I mean... Arrgg! Ye young scallywag! What say we learn how to steer this ship!

Y'arrr... Me Wish th' whole documentation were in pirate talk... But it be not...

Installation
---------------

Voyager is super easy to install. First off, you'll need to have all the requirements needed to install a Laravel app. Then, after creating your new Laravel application you can include the Voyager package with the folowing command: 

```
composer require tcg/voyager
```

Next make sure to create a new database and add your database credentials to your .env file:

```
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Add the Voyager service provider as well as the Image Intervention service provider to the config/app.php file in the `'providers' => [` array:

```
TCG\Voyager\VoyagerServiceProvider::class,
Intervention\Image\ImageServiceProvider::class,
```

Lastly, we can install voyager by running

```
php artisan voyager:install
```

And we're all good to go! 

Start up a local development server with `php artisan serve` And, visit http://localhost:8000/admin and you can login with the following login credentials:

```
**email:** admin@admin.com
**password:** password
```


Database Tools
---------------

Voyager has some awesome database tools which allow you to Add/Edit/Delete or view current database tables. The other cool part of Voyager is that you can add BREAD or (Browse, Read, Edit, Add, & Delete) functionality to any of your tables.

### DATABASE

Inside of your admin panel you can visit Tools->Database and you'll be able to view all your current tables in your database. You may also click on 'Create a New Table' to create a new table in your database.

If you click the table name you can view the current schema. Additionally you can click on the View, Edit, or Delete buttons to perform that action for that table.

You may also choose to Add BREAD (Browse, Read, Edit, Add, & Delete) for any of your database tables. Once a table already has BREAD you may choose to edit the current BREAD or Delete the BREAD for that table.

### BREAD

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
```
{
    "on" : "On Text",
    "off" : "Off Text",
    "checked" : "true"
}
```

In Voyager a Check Box is converted into a toggle switch, and as you can see above the `on` key will contain the value when the toggle switch is on, and the `off` will contain the value that is set when the switch is off. If `checked` is set to *true* the checkbox will be toggle on; otherwise by default it will be off.

#### Drop Down
```
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
```
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
```
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