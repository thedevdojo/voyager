# **V**oyager - The Missing Laravel Admin
Made with ❤️ by [The Control Group](https://www.thecontrolgroup.com)

[![Build Status](https://travis-ci.org/the-control-group/voyager.svg?branch=master)](https://travis-ci.org/the-control-group/voyager)

![Voyager Screenshot](https://raw.githubusercontent.com/the-control-group/voyager/gh-pages/images/screenshot.png)

Video Demo Here: https://devdojo.com/episode/laravel-admin-package-voyager

Join our Slack chat: https://voyager-slack-invitation.herokuapp.com/

Laravel Admin & BREAD System (Browse, Read, Edit, Add, & Delete), made for Laravel 5.3.

![Voyager Logo](https://s3.amazonaws.com/thecontrolgroup/voyager.png)

After creating your new Laravel application you can include the Voyager package with the following command: 

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

Lastly, we can install voyager by running

```
php artisan voyager:install
```

If you don't have composer installed and use composer.phar instead, do the following:

```
composer.phar dump-autoload
```

And then:
```
php artisan db:seed
```

And we're all good to go! 

Start up a local development server with `php artisan serve` And, visit http://localhost:8000/admin and you can login with the following login credentials:

```
**email:** admin@admin.com
**password:** password
```
