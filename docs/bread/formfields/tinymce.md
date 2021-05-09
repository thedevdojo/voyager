# TinyMCE

If you want to customize TinyMCE within Voyager, you can do so by adding a [additional JS file](../../customization/additional-css-js.md) to your config.

In this file you have to define a function like

```javascript
function tinymce_init_callback(editor)
{
    //...
}
```

If you need to manipulate TinyMCE **before** it was initialized, you can use

```javascript
function tinymce_setup_callback(editor)
{
    //...
}
```

If you want to customize TinyMCE init configuration Options you can merge your custom options in BREAD details as follow:

```php
{
    "tinymceOptions" : {
        "name": "value"
    }
}
```

If you want to use tinyMCE outside it's default template `rich_text_box` you'll need initialize it with:

```javascript
tinymce.init(window.voyagerTinyMCE.getConfig());
```

For all possible variables, functions and configuration Options please refer to the [TinyMCE documentation](https://www.tinymce.com/docs/api/tinymce/tinymce.editor/).
