# Enabling Soft-Delete

This is only to assist with enabling soft-deletion for your models within Voyager. Please refer to the [Laravel documentation](https://laravel.com/docs/eloquent#soft-deleting) for specifics.

## Table Configurations in Voyager

When creating a table using the Database Manager you've selected the 'Add Soft Deletes' button and then when adding the BREAD functionality to that table you've added a Model Name, you only have to edit your Model file to fully enable Soft-Delete on that table.

## Editing the Table's Model

A default model will look like this:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class YourModelName extends Model
{

}
```

Just turn it into:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Documento extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
```

And from now on, every time you delete a record from that table, it won't actually be deleted, only the `deleted_at` column will be written with the current timestamp.

