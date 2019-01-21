# Upgrading

## Upgrading 1.1 to 1.2

### Update your Composer.json
To update to the latest version inside of your composer.json file make sure to update the version of Voyager inside the require declaration inside of your composer.json to:

`tcg/voyager": "1.2.*`

And then run composer update

### Update Configuration
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

You can now define an array of mimetypes which are allowed to be uploaded through the media-manager.
```php
'allowed_mimetypes' => '*', //All types can be uploaded
/*'allowed_mimetypes' => [
  'image/jpeg',
  'image/png',
  'image/gif',
  'image/bmp',
  'video/mp4',
],*/
```

Compass is now switched off automatically when the environment is not `local`.  
This can be overriden by the following new config-key:
```php
'compass_in_production' => true,
```

### Deprecation
`can`, `canOrAbort`, `canOrFail` in the Voyager facade were all removed in favor of Policies and Gates.  
Please refer to the [Laravel documentation](https://laravel.com/docs/authorization).

### User BREAD
The User BREAD now has its own controller.
Please update your User BREAD to use `TCG\Voyager\Http\Controllers\VoyagerUserController` as the controller:
![](../.gitbook/assets/upgrade_controller.jpg)

### Final Steps
Voyager changed its way on how to load assets.  
Assets don't get published anymore, instead they are loaded directly from the package.  
Because of that, you can safely remove everything from your `public/vendor/tcg/voyager` folder.  
Also you can remove the `assets_path` config-key from `config/voyager.php`.

## Troubleshooting

Be sure to ask us on our slack channel if you are experiencing any issues and we will try and assist. Thanks.
