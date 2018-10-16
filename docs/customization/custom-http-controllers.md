# Using custom HTTP controllers

You can use your own Controller by extending Voyager's Controllers. To do it, first define your controller Namespace at `config/voyager.php` :

```php
/*
|--------------------------------------------------------------------------
| Controllers config
|--------------------------------------------------------------------------
|
| Here you can specify voyager controller settings
|
*/

'controllers' => [
    'namespace' => 'App\\Http\\Controllers\\Voyager',
],
```

then run `php artisan voyager:controllers`, voyager will now use the child controllers which will be created at `App/Http/Controllers/Voyager`

