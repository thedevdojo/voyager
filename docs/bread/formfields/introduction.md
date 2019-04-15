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
