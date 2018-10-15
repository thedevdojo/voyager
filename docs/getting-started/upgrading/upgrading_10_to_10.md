# Version 1.0 to 1.0.\*

To update to the latest version inside of your composer.json file make sure to update the version of voyager inside the require declaration inside of your composer.json to:

```text
"tcg/voyager": "1.0.*"
```

And then run

```text
composer update tcg/voyager
```

Next, you may want to be sure that you have all the latest published assets. To re-publish the voyager assets you can run the following command:

```text
php artisan vendor:publish --tag=voyager_assets --force
```

Lastly, you may wish to clear your view cache by running the following command:

```text
php artisan view:clear
```

