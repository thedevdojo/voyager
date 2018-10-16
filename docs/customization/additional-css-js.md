# Additional CSS and JS

As of the latest version you can now add additional CSS and JS files to the voyager master blade without having to copy or modify the blade file itself removing potential manual migration headaches later on. The CSS and JS files are added _after_ any Voyager assets so you can override styles and functionality comfortably.

This is all handled via the `voyager.php` config, if you have not started with a fresh install of `0.11.10+` you will need to manually add this to your config.

```php
    // Here you can specify additional assets you would like to be included in the master.blade
    'additional_css' => [
        //'css/custom.css',
    ],

    'additional_js' => [
        //'js/custom.js',
    ],
```

You may want to look at the `/vendor/tcg/voyager/publishable/config/voyager.php` to see the config layout and check for any missing options.

