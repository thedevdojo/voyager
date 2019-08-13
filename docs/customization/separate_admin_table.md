# Separate admin table

Since Voyager 1.2 you can keep your application users and voyager admins in a seperate table.

First, create your customized model, for example `VoyagerUser`, and make sure it extends `\TCG\Voyager\Models\User`:

```php
class VoyagerUser extends \TCG\Voyager\Models\User
{

}
```

Make sure that the columns `name`, `email` and `password` are inside your migration file:

```php
public function up()
{
    Schema::create('voyager_users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password')->nullable();
        $table->timestamps();
    });
}
```

Register your `VoyagerUser` model in the `boot` method of your `AppServiceProvider`:

    Voyager::useModel('User', \App\VoyagerUser::class);

Create a new voyager [guard](https://laravel.com/docs/5.8/authentication#adding-custom-guards) in `config/auth.php`, for example a guard called `admin`:

```php
  'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'admin' => [
          'driver' => 'session',
          'provider' => 'voyager',
        ]
    ],

  'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        'voyager' => [
            'driver' => 'eloquent',
            'model' => App\VoyagerUser::class,
        ],
    ],
```

Register you new guard to your `AuthServiceProvider` as explained [here](https://voyager-docs.devdojo.com/customization/custom-guard).

If your `admin` guard is not the default guard of your app, and is using a driver other then `session`, then
its necessary to specifiy the guard in `config/voyager.php`

    'guard' => 'admin',

