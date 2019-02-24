# Overriding Formfields

You can specify a custom view to be used for a BREAD-field.  
To do so, you have to specify the `view` attribute for your desired field:
```
{
    "view": "my_view"
}
```

This will then load `my_view` instead of the formfield.

You get plenty of data passed to your view for you to use:

- `$action` can be `browse`, `read`, `edit`, `add` or `order`
- `$content` the content for this field
- `$dataType` the DataType
- `$dataTypeContent` the whole model-instance
- `$row` the DataRow

{% hint style="info" %}
**Developing a custom formfield?**  
If you are developing a custom formfield and want to customize any of the views, you can do so by merging `view` into `$options` in your formfields `createContent()` method.
{% endhint %}
