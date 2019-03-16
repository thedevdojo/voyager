# Formfields

## Description

All types can include a description in order to help your future self or other users using your Voyager admin panel to understand exactly what a specific BREAD input field is for, this can be defined in the `Optional Details` JSON input field:

```php
{
    "description": "A helpful description text here for your future self."
}
```

# Something

When Editing Your Browse, Read, Edit, Add, and Delete Rows you have a select box that allows you to include additional details or options for your datatype. This textarea accepts JSON and it applies to the following types of inputs:

* Text \(Text Box, Text Area, Rich Textbox and Hidden\)
* Check Box
* Drop Down
* Radio Button
* Image
* Date

Find out how to use these additional details below:

## Text \(Text Box, Text Area, Rich Textbox and Hidden\)

```php
{
    "default" : "Default text"
}
```

Text Box, Text Area, Rich Textbox and Hidden are all kind of texts inputs. In the JSON above you can specify the `default` value of the input.
