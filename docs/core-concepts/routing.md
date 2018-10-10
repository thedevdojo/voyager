# Routing

After running the voyager installer you will see a few new routes that have been added to your `routes/web.php` file which look like the following:

```php
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
```

This is where the voyager routes will be rendered. You can change the `admin` prefix if you desire, or set any other route configuration you need, such as `middleware` or `domain`.

When creating a new BREAD type and specifying a slug for that BREAD, you can then visit that route from the following link:

```text
URL/admin/slug-name
```

As an example, if we have a `products` table and we specified the slug to be `products`. You will now be able to visit the following URL:

```text
URL/admin/products
```

{% hint style="info" %}
**Notice**  
You may not see a link to your newly created routes or BREAD inside your admin menu. To create a new link in your admin menu visit the documentation for the menu section.
{% endhint %}

