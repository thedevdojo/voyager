<p align="center"><a href="https://voyager.devdojo.com" target="_blank"><img width="400" src="https://s3.amazonaws.com/thecontrolgroup/voyager.png"></a></p>

> Voyager is built with Vue and Bootstrap. If you are looking for a Laravel Starter Kit built that uses Livewire and Tailwind, you may be interested in checking out [Genesis](https://github.com/thedevdojo/genesis)

<p align="center"><a href="https://github.com/thedevdojo/genesis" target="_blank"><img src="https://github.com/thedevdojo/voyager/assets/601261/6ffa6ac4-ea1e-4c8a-8360-b347377b8201" height="auto" width="100%"></a></p>

<p align="center">
<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/downloads.svg?format=flat" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/v/stable.svg?format=flat" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/license.svg?format=flat" alt="License"></a>
<a href="https://github.com/larapack/awesome-voyager"><img src="https://cdn.rawgit.com/sindresorhus/awesome/d7305f38d29fed78fa85652e3a63e154dd8e8829/media/badge.svg" alt="Awesome Voyager"></a>
</p>

# **V**oyager - The Missing Laravel Admin
Made with ❤️ by [The Control Group](https://www.thecontrolgroup.com)

![Voyager Screenshot](https://s3.amazonaws.com/thecontrolgroup/voyager-screenshot.png)

Website & Documentation: https://voyager.devdojo.com/

Video Tutorial Here: https://voyager.devdojo.com/academy/

Join our Slack chat: https://voyager-slack-invitation.herokuapp.com/

View the Voyager Cheat Sheet: https://voyager-cheatsheet.ulties.com/

<hr>

Laravel Admin & BREAD System (Browse, Read, Edit, Add, & Delete), supporting Laravel 8 and newer!

> Want to use Laravel 6 or 7? Use [Voyager 1.5](https://github.com/the-control-group/voyager/tree/1.5)

> Want to use Laravel 8 or 9? Use [Voyager 1.6](https://github.com/the-control-group/voyager/tree/1.6)

> Want to use Laravel 10? Use [Voyager 1.7](https://github.com/the-control-group/voyager/tree/1.7)

## Installation Steps

> [!WARNING]
> As of Laravel 11 the Doctrine DBAL package has been removed from Laravel. This currently breaks the Voyager Database Manager. We are working on a fix for this, but in the meantime, you can just use the Laravel Database migrations to manage your database instead.

### 1. Require the Package

After creating your new Laravel application you can include the Voyager package with the following command:

```bash
composer require tcg/voyager
```

> If you are installing this on Laravel 10, we are working on getting a permanent release available; however, you can still use this with Larvel 10 by requiring the following:

```bash
composer require tcg/voyager dev-1.6-l10
```

### 2. Add the DB Credentials & APP_URL

Next make sure to create a new database and add your database credentials to your .env file:

```
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

You will also want to update your website URL inside of the `APP_URL` variable inside the .env file:

```
APP_URL=http://localhost:8000
```

### 3. Run The Installer

Lastly, we can install voyager. You can do this either with or without dummy data.
The dummy data will include 1 admin account (if no users already exists), 1 demo page, 4 demo posts, 2 categories and 7 settings.

To install Voyager without dummy simply run

```bash
php artisan voyager:install
```

If you prefer installing it with dummy run

```bash
php artisan voyager:install --with-dummy
```

And we're all good to go!

Start up a local development server with `php artisan serve` And, visit [http://localhost:8000/admin](http://localhost:8000/admin).

## Creating an Admin User

If you did go ahead with the dummy data, a user should have been created for you with the following login credentials:

>**email:** `admin@admin.com`   
>**password:** `password`

NOTE: Please note that a dummy user is **only** created if there are no current users in your database.

If you did not go with the dummy user, you may wish to assign admin privileges to an existing user.
This can easily be done by running this command:

```bash
php artisan voyager:admin your@email.com
```

If you did not install the dummy data and you wish to create a new admin user, you can pass the `--create` flag, like so:

```bash
php artisan voyager:admin your@email.com --create
```

And you will be prompted for the user's name and password.
