# Upgrading

## Upgrading 1.1 to 1.x

### Update your Composer.json

To update to the latest version inside of your composer.json file make sure to update the version of voyager inside the require declaration inside of your composer.json to:

```text
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/the-control-group/voyager.git"
        }
    ],

    "require": {
        "tcg/voyager": "1.x-dev"
    }
}
```

And then run `composer update`

Or simply run:

```text
composer require tcg/voyager:1.x-dev
```

### Update Configuration {#update-configuration}

The `voyager.php` configuration file has had a few changes.

```php
'storage' => [
    'disk' => 'public',
],
```

is now

```php
'storage' => [
    'disk' => env('FILESYSTEM_DRIVER', 'public'),
],
```

Also, the option

```php
'database' => [
    'autoload_migrations' => true,
]
```

was added. This allows you to exclude Voyagers migration-files from loading when running `php artisan migrate`.

### **Final Steps**

Next, you may want to be sure that you have all the latest published assets. To re-publish the voyager assets you can run the following command:

```text
php artisan vendor:publish --tag=voyager_assets --force
```

Then you may wish to clear your view cache by running the following command:

```text
php artisan view:clear
```

## Troubleshooting

Be sure to ask us on our slack channel if you are experiencing any issues and we will try and assist. Thanks.

