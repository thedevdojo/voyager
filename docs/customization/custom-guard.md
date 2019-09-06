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


# Example - using a different model and table for Admins

First you have to create a new table. Let's call it `admins`:  
```php
<?php
Schema::create('admins', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->bigInteger('role_id')->unsigned()->nullable();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('avatar')->nullable()->default('users/default.png');
    $table->string('password')->nullable();
    $table->string('remember_token')->nullable();
    $table->text('settings')->nullable()->default(null);
    $table->timestamps();
    $table->foreign('role_id')->references('id')->on('roles');
});
```

and a model which extends Voyagers user-model:

```php
<?php

namespace App;

class Admin extends \TCG\Voyager\Models\User
{

}
```

Next, create a guard (see above) named `admin`:
```
'guards' => [
    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],

    // ...
],
```
And a user provider called `admins`:
```
'providers' => [
    'admins' => [
        'driver' => 'eloquent',
        'model' => App\Admin::class,
    ],

    // ...
],
```

Next you have to tell Voyager to use your model as the user-model and your new guard.  
Open you `AppServiceProvider.php` and add the following to the `register` method:

```php
public function register()
{
    \TCG\Voyager\Facades\Voyager::useModel('User', \App\Admin::class);

    $this->app->singleton('VoyagerGuard', function () {
        return 'admin';
    });
}
```

Next, go to your database and search for the User-BREAD in the `data_types` table and change the model to `App\Admin`.  
The last step is to remove the User-BREAD or to ungrant permissions for all Roles and create a new BREAD for your Admin-Table.