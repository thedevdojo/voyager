# Installation

Voyager is super easy to install. After creating your new Laravel application you can include the Voyager package with the following command:

```bash
composer require tcg/voyager
```

Next make sure to create a new database and add your database credentials to your .env file, you will also want to add your application URL in the `APP_URL` variable:

```text
APP_URL=http://localhost
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Finally, we can install Voyager. You can choose to install Voyager with dummy data or without the dummy data. The dummy data will include 1 admin account \(if no users already exist\), 1 demo page, 4 demo posts, 2 categories and 7 settings.

To install Voyager without dummy data simply run

```bash
php artisan voyager:install
```

If you prefer installing it with the dummy data run the following command:

```bash
php artisan voyager:install --with-dummy
```

{% hint style="danger" %}
**Specified key was too long error**  
If you see this error message you have an outdated version of MySQL, use the following solution: [https://laravel-news.com/laravel-5-4-key-too-long-error](https://laravel-news.com/laravel-5-4-key-too-long-error)
{% endhint %}

And we're all good to go!

Start up a local development server with `php artisan serve` And, visit the URL [http://localhost:8000/admin](http://localhost:8000/admin) in your browser.

If you installed with the dummy data, a user has been created for you with the following login credentials:

> **email:** `admin@admin.com`  
> **password:** `password`

{% hint style="info" %}
**Quick note**  
A dummy user is **only** created if there are no current users in your database.
{% endhint %}

If you did not go with the dummy user, you may wish to assign admin privileges to an existing user. This can easily be done by running this command:

```bash
php artisan voyager:admin your@email.com
```

If you wish to create a new admin user you can pass the `--create` flag, like so:

```bash
php artisan voyager:admin your@email.com --create
```

And you will be prompted for the users name and password.

## Advanced

This section is meant for users who are installing Voyager on an already existing Laravel installation or for users who want to perform a manual install. If this is not the case, you should go back to the [installation](installation.md) documentation or skip this section.

The first thing you should do is publish the assets that come with Voyager. You can do that by running the following commands:

```bash
php artisan vendor:publish --provider="TCG\Voyager\VoyagerServiceProvider"
php artisan vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravelRecent"
```

Next, call `php artisan migrate` to migrate all Voyager table.

{% hint style="info" %}
If you want to change migrations, for example to use a different table for users, don't migrate. Instead copy Voyagers migrations to `database/migrations`, make your changes, turn off the config option `database.autoload_migrations` and then migrate.
{% endhint %}

Now, open your User-Model \(usually `app/User.php`\) and make the class extend `\TCG\Voyager\Models\User` instead of `Authenticatable`.

```php
<?php

class User extends \TCG\Voyager\Models\User
{
    // ...
}
```

The next step is to add Voyagers routes to your `routes/web.php` file:

```php
<?php

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
```

Now run  
`php artisan db:seed --class=VoyagerDatabaseSeeder`  
to seed some necessary data to your database,  
`php artisan hook:setup`  
to install the hooks system, and  
`php artisan storage:link`  
to create the storage symlink to your public folder.

After that, run `composer dump-autoload` to finish your installation!

