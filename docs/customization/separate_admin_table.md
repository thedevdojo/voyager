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
        $table->bigInteger('role_id')->unsigned()->nullable()
        $table->string('name');
        $table->string('email')->unique();
        $table->string('avatar', 191)->nullable()->default('users/default.png');
        $table->string('password')->nullable();
        $table->string('remember_token', 191)->nullable();
        $table->text('settings')->nullable()->default(null);
        $table->timestamps();

	$table->foreign('role_id')->references('id')->on('roles');
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

Register your new guard to your `AuthServiceProvider` as explained [here](https://voyager-docs.devdojo.com/customization/custom-guard).

If you have set `user.add_default_role_on_register` to true in your config, you need to set `user.namespace` to the namespace of your voyager user (e.g. `'\App\VoyagerUser'`).

**Note 1**: Changing the default guard to anything other then the voyager guard is only supported for `session` driver in 1.2.

**Note 2**: It is assumend that the middleware `admin.user` is used for any admin route (except login).

