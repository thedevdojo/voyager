# TinyMCE

If you want to customize TinyMCE within Voyager, you can do so by adding a [additional JS file](additional-css-js.md) to your config.

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

Now you can control the TinyMCE instance\(s\) on the page. For all possible variables and functions, please refer to the [TinyMCE documentation](https://www.tinymce.com/docs/api/tinymce/tinymce.editor/).

