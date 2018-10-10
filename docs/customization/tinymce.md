# TinyMCE

If you want to customize TinyMCE within Voyager, you can do so by adding a [additional JS file](https://github.com/emptynick/voyager/tree/735a22e97d81b204cc668c421aa06e1268182ed9/additional-css-js/README.md) to your config.

In this file you have to define a function like

```text
function tinymce_init_callback(editor)
{
    //...
}
```

Now you can control the TinyMCE instance\(s\) on the page. For all possible variables and functions, please refer to the [TinyMCE documentation](https://www.tinymce.com/docs/api/tinymce/tinymce.editor/).

