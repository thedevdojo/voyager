# Custom guard

Starting with Voyager 1.2 you can define a \(custom\) guard which is used throughout Voyager.  
To do so, just bind the name of your auth-guard to `VoyagerGuard`.  
First, make sure you have defined a guard as per the [Laravel documentation](https://laravel.com/docs/authentication#adding-custom-guards).  
After that open your `AuthServiceProvider` and add the following to the register method:

```php
$this->app->singleton('VoyagerGuard', function () {
    return 'your-custom-guard-name';
});
```

Now this guard is used instead of the default guard.

