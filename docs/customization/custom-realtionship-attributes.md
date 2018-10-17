# Custom relationship attributes

With Voyager 1.1 you are able to define additional attributes which you can show in a relationship.

For example a `Post` has an `Author` and you want to display the `Users` full-name. To do so, we first need to [define an Accessor](https://laravel.com/docs/eloquent-mutators#defining-an-accessor)

```php
public function getFullNameAttribute()
{
    return "{$this->first_name} {$this->last_name}";
}
```

After that we need to tell Voyager that there is an accessor we want to use:

```php
public $additional_attributes = ['full_name'];
```

Thats it! You can now select `full_name` in your Relationship.

