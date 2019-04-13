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
