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

## Width

You can define define the width of a formfield as a number between 1 and 12, where 12 is full-width and 6 is half-width:

```php
{
    "width" : 6
}
```

## Default value

Most formfields allow you to define a default value when adding an entry:

```php
{
    "default" : "Default text"
}
```

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
