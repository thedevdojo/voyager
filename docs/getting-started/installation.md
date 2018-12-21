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

{% hint style="info" %}
**Using Laravel 5.4?**  
If you are installing with Laravel 5.4 you will need to [add the Service Provider manually](installation.md#adding-the-service-provider). Otherwise, if you are on 5.5 this happens automatically thanks to package auto-discovery.
{% endhint %}

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

If you did not go with the dummy user, you may wish to assign admin priveleges to an existing user. This can easily be done by running this command:

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
php artisan vendor:publish --provider=VoyagerServiceProvider
php artisan vendor:publish --provider=ImageServiceProviderLaravel5
```

## Adding the Service Provider

{% hint style="info" %}
**This is only required if you are using Laravel 5.4!**  
If you are on Laravel 5.5+ you can skip this step.
{% endhint %}

To add the Voyager Service Provider open up your application `config/app.php` file and add `TCG\Voyager\VoyagerServiceProvider::class,` in the `providers` array like so:

```php
<?php

'providers' => [
    // Laravel Framework Service Providers...
    //...

    // Package Service Providers
    TCG\Voyager\VoyagerServiceProvider::class,
    // ...

    // Application Service Providers
    // ...
],
```

