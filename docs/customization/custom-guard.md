# Custom guard

Starting with Voyager 1.2 you can define a \(custom\) guard which is used throughout Voyager.  
To do so, just bind your auth-guard to `VoyagerAuth`.  
Open your `AuthServiceProvider` and add the following to the register method:

```php
$this->app->singleton('VoyagerAuth', function () {
    return Auth::guard('your-custom-guard');
});
```

Now this guard is used instead of the default guard.

