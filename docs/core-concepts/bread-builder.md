# BREAD Builder

When adding or editing the current BREAD for a database table you will first see the BREAD info which allows you to set the Display Names, slug, icon, `Model` and Controller Namespace, Policy Name. You can also choose if you would like to Generate permissions for that BREAD type.

![](../.gitbook/assets/bread_details.png)

When you scroll down you will see each of the rows that are associated with that table where you can select where in your views you want to see each of those fields:

* BROWSE \(field will show up when you browse the current data\)
* READ \(field will show when you click to view the current data\)
* EDIT \(field will be visible and allow you to edit the data\)
* ADD \(field will be visible when you choose to create a new data type\)
* DELETE \(doesn't pertain to delete so this can be checked or unchecked\)

![](../.gitbook/assets/bread_fields.png)

You may also choose to specify what form type you want to use for each field. This can be a TextBox, TextArea, Checkbox, Image, and many other types of form elements.

Each field also has additional details or options that can be included. These types are checkbox, dropdown, radio button, and image.

## Additional Field Options

When Editing Your Browse, Read, Edit, Add, and Delete Rows you have a select box that allows you to include additional details or options for your datatype. This textarea accepts JSON and it applies to the following types of inputs:

* Text \(Text Box, Text Area, Rich Textbox and Hidden\)
* Check Box
* Drop Down
* Radio Button
* Image
* Date

Find out how to use these additional details below:

### Text \(Text Box, Text Area, Rich Textbox and Hidden\)

```php
{
    "default" : "Default text"
}
```

Text Box, Text Area, Rich Textbox and Hidden are all kind of texts inputs. In the JSON above you can specify the `default` value of the input.

### Check Box

```php
{
    "on" : "On Text",
    "off" : "Off Text",
    "checked" : "true"
}
```

In Voyager a Check Box is converted into a toggle switch, and as you can see above the `on` key will contain the value when the toggle switch is on, and the `off` will contain the value that is set when the switch is off. If `checked` is set to _true_ the checkbox will be toggle on; otherwise by default it will be off.

### Drop Down

```php
{
    "default" : "option1",
    "options" : {
        "option1": "Option 1 Text",
        "option2": "Option 2 Text"
    }
}
```

When specifying that an input type should be a dropdown you will need to specify the values of that dropdown. In the JSON above you can specify the `default` value of the dropdown if it does not have a value. Additionally, in the `options` object you will specify the _value_ of the option on the **left** and the _text_ to be displayed on the **right**.

### Radio Button

```php
{
    "default" : "radio1",
    "options" : {
        "radio1": "Radio Button 1 Text",
        "radio2": "Radio Button 2 Text"
    }
}
```

The Radio button is exactly the same as the dropdown. You can specify a `default` if one has not been set and in the `options` object you will specify the _value_ of the option on the **left** and the _text_ to be displayed on the **right**.

### Image

```php
{
    "resize": {
        "width": "1000",
        "height": null
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

**resize** If you want to specify a size you will need to include that in the `resize` object. If you set either **height** or **width** to null it will keep the aspect ratio based on the width or height that is set. So, for the example above the `width` is set to `1000` pixels and since the `height` is set to `null` it will resize the image width to 1000 pixels and resize the height based on the current aspect ratio.

**quality** If you wish to compress the image with a percentage quality you can specify that percentage in the `quality` key. Typically between 70 and 100% there is little notice of image quality, but the image size may be dramatically lower.

**upsize** This is only valid if you have set your image to be resized. If you specify your image to resized to 1000 pixels and the image is smaller than 1000 pixels by default it will not upsize that image to the 1000 pixels; however, if you set `upsize` to true. It will upsize all images to your specified resize values.

**thumbnails** Thumbnails takes an array of objects. Each object is a new thumbnail that is created. Each object contains 2 values, the `name` and `scale` percentage. The `name` will be attached to your thumbnail image \(as an example say the image you uploaded was ABC.jpg a thumbnail with the `name` of `medium` would now be created at ABC-medium.jpg\). The `scale` is the percentage amount you want that thumbnail to scale. This value will be a percentage of the _resize_ width and height if specified.

### Date & Timestamp

```php
{
    "format" : "%Y-%m-%d"
}
```

The date & timestamp input field is where you can input a date. In the JSON above you can specify the `format` value of the output of the date. It allows you to display a formatted `date` in browse and read views, using Carbon's `formatLocalized()` method

## Description

All types can include a description in order to help your future self or other users using your Voyager admin panel to understand exactly what a specific BREAD input field is for, this can be defined in the `Optional Details` JSON input field:

```php
{
    "description": "A helpful description text here for your future self."
}
```

## Validation

Inside of the _Optional Details_ section for each row in your BREAD you can also specify validation rules with some simple JSON. Here is an example of how to add a validation rule or _required_ and _max length of 12_

```php
{
    "validation": {
        "rule": "required|max:12"
    }
}
```

Additionally, you may wish to add some custom error messages which can be accomplished like so:

```php
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

```php
{
    "validation": {
        "rules": [
            "required",
            "max:12"
        ]
    }
}
```

You can find a list of all available validation rules in the [Laravel docs](https://laravel.com/docs/validation#available-validation-rules).

## Generating Slugs

Using the bread builder you may wish to automatically generate slugs of a certain input. Lets say you have some posts, which have a title and a slug. If you want to automatically generate the slug from the title attribute, you may include the following _Optional Details_:

```php
{
    "slugify": {
        "origin": "title",
        "forceUpdate": true
    }
}
```

This will automatically generate the slug from the input of the `title` field. If a slug does already exists, it will only be updated if `forceUpdate` is set enabled, by default this is disabled.

## Relationships

Using the BREAD builder you can easily create Relationships between tables. At the bottom of the page you will see a new button that says 'Create Relationship'

![](../.gitbook/assets/bread_relationship.png)

{% hint style="info" %}
**Notice**  
If you have not yet created the BREAD for the table yet, it will need to be created first and then you can come back after creating the BREAD to add the relationship. Otherwise you'll end up with a notification which looks like the following.
{% endhint %}

![](../.gitbook/assets/bread_relationship_no_bread.png)

So, after the BREAD has already been created you will then be able to create a new relationship. After you click on the 'Create a Relationship' button. You will see a new Modal window that looks like the following:

![](../.gitbook/assets/bread_relationship_form.png)

You will first specify which type of relationship this is going to be, then you will select the table you are referencing and which Namespace that belongs to that table. You will then select which row combines those tables.

You can also specify which columns you would like to see in the dropdown or the multi-select.

Now, you can easily create `belongsTo`, `belongsToMany`, `hasOne`, and `hasMany` relationships directly in Voyager.

## Tagging

Tagging gives you the possibility to add new items to a Belongs-To-Many relationship directly when editing or adding a BREAD.

To activate this function, you simply have to enable `Tagging` in the relationship details

![](../.gitbook/assets/tagging.jpg)

After that you can enter a free-text into the select and hit enter to save a new relationship.

{% hint style="info" %}
**Be aware:**

This only stores the `display-column` so you have to make sure that all other fields are either nullable or have a default value.
{% endhint %}

## Null Values

You might want to save an input field into the database as a `null` value instead of an empty string.

Simply enough, inside the BREAD you can include the following _Optional Details_ for the field:

```php
{
    "null": ""
}
```

This will turn an empty string into a `null` value. However you might want to be able to add both an empty string and a `null` value to the database for that field. However you have to choose a replacement for the `null` value, but it can be anything you wish. For example, if you want a field to change a string \(ex. `Nothing`\) into a `null` value you could include the following _Optional Details_ for that field:

```php
{
    "null": "Nothing"
}
```

Now entering `Nothing` into the field will end up as a `null` value in the database.

## Display options

There are also a few options that you can include to change the way your BREAD is displayed. You can add a `display` key to your json object and change the width of the particular field and even specify a custom ID.

```php
{
    "display": {
        "width": "3",
        "id": "custom_id"
    }
}
```

The width is displayed on a 12-grid system. Setting it with a width of 3 will span 25% of the width.

The **id** will let you specify a custom id wrapper around your element. example:

```markup
<div id="custom_id">
    <!-- Your field element -->
</div>
```

## Ordering Bread Items

You can define the default order for browsing BREADs and order your BREAD items with drag-and-drop.  
For this you need to change the settings for your BREAD first:

![](../.gitbook/assets/bread_settings_order.png)

**Order column** is the field in your table where the order is stored as an integer.  
**Order display column** is the field which is shown in the drag-drop list.  
**Order direction** the direction in which the field is ordered.

After this you can go to your BREAD-browse page and you will see a button **Order.**  
Clicking this button will bring you to the page where you can re-arrange your items:

![](../.gitbook/assets/bread_order.png)
