# Checkbox

```php
{
    "on" : "On Text",
    "off" : "Off Text",
    "checked" : true
}
```

In Voyager a Checkbox is converted into a toggle switch, and as you can see above the `on` key will contain the value when the toggle switch is on, and the `off` will contain the value that is set when the switch is off. If `checked` is set to _true_ the checkbox will be toggle on; otherwise by default it will be off.

# Multiple Checkbox

```php
{
    "checked" : true,
    "options": {
        "checkbox1": "Checkbox 1 Text",
        "checkbox2": "Checkbox 2 Text"
    }
}
```

You can create as many checkboxes as you want.  

# Radio Button

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
