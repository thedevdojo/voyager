# **V**oyager - The Missing Laravel Admin

Laravel Admin & BREAD System. (Browse, Read, Edit, Add, & Delete)

![Voyager Logo](https://s3.amazonaws.com/thecontrolgroup/voyager.png)

After creating your new Laravel application you can include the Voyager package with the folowing command: 

```
composer require tcg/voyager
```

Next make sure to create a new database and add your database credentials to your .env file:

```
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Add the Voyager service provider as well as the Image Intervention service provider to the config/app.php file in the `'providers' => [` array:

```
    TCG\Voyager\VoyagerServiceProvider::class,
    Intervention\Image\ImageServiceProvider::class,
```

Then in the `'aliases' => [` array, add the following aliases:

```
'Menu' => TCG\Voyager\Models\Menu::class,
'Voyager' => TCG\Voyager\Voyager::class,
```

Optionally if you wish to have the front-end authentication scaffolding provided by laravel you can run:

```
php artisan make:auth
```

Then, we'll need to publish our voyager files to be loaded into your app

```
php artisan vendor:publish
```

Finally, lets run our migrations

```
php artisan migrate
```

And before we run the database seed, we need to run the following command:

```
composer dump-autoload
```

Now, let's run our database seeds:

```
php artisan db:seed --class=VoyagerDatabaseSeeder
```

Next, we need to add our symbolic link so our images will be located inside of our storage directory:

```
php artisan storage:link
```

And we're all good to go! 

Start up a local development server with `php artisan serve` And, visit http://localhost:8000/admin and you can login with the following login credentials:

```
**email:** admin@admin.com
**password:** password
```
