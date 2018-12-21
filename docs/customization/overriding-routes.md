# Overriding Routes

You can override any Voyager routes by writing the routes you want to overwrite below `Voyager::routes()`. For example if you want to override your post LoginController:

```php
Route::group(['prefix' => 'admin'], function () {
   Voyager::routes();

   // Your overwrites here
   Route::post('login', ['uses' => 'MyAuthController@postLogin', 'as' => 'postlogin']);
});
```

