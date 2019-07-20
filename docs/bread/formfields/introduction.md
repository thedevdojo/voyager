# Formfields

Formfields are the hearth of Voyagers BREAD-system.  
Each formfield represents a field in your database-table and one input (or output) in BREAD.  
To tweak your formfields you can insert JSON options which are described in the following pages.  

All formfields share a handful options:

## Description

All types can include a description in order to help your future self or other users using your Voyager admin panel to understand exactly what a specific BREAD input field is for, this can be defined in the `Optional Details` JSON input field:

```php
{
    "description": "A helpful description text here for your future self."
}
```

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

## Default value

Most formfields allow you to define a default value when adding an entry:

```php
{
    "default" : "Default text"
}
```

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

## Custom view

You can specify a custom view to be used for a formfield.  
To do so, you have to specify the `view` attribute for your desired field:

```text
{
    "view": "my_view"
}
```

This will then load `my_view` from `resources/views` instead of the formfield.

You get plenty of data passed to your view for you to use:

* `$action` can be `browse`, `read`, `edit`, `add` or `order`
* `$content` the content for this field
* `$dataType` the DataType
* `$dataTypeContent` the whole model-instance
* `$row` the DataRow

{% hint style="info" %}
**Developing a custom formfield?**  
If you are developing a custom formfield and want to customize any of the views, you can do so by merging `view` into `$options` in your formfields `createContent()` method.
{% endhint %}
