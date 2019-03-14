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

## Check Box

```php
{
    "on" : "On Text",
    "off" : "Off Text",
    "checked" : true
}
```

In Voyager a Check Box is converted into a toggle switch, and as you can see above the `on` key will contain the value when the toggle switch is on, and the `off` will contain the value that is set when the switch is off. If `checked` is set to _true_ the checkbox will be toggle on; otherwise by default it will be off.

## Drop Down

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

## Radio Button

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

## Image

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

## Date & Timestamp

```php
{
    "format" : "%Y-%m-%d"
}
```

The date & timestamp input field is where you can input a date. In the JSON above you can specify the `format` value of the output of the date. It allows you to display a formatted `date` in browse and read views, using Carbon's `formatLocalized()` method

