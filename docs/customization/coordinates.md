# Coordinates

With Voyager you are able to store coordinates and select them from a map.  
To do so, you first need to make sure that the column in your table is either `GEOMETRY` or `POINT`.

After that you have to include the Spatial-Trait in your Model and define the column:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Spatial;

class Category extends Model
{
    use Spatial;

    protected $spatial = ['your_column'];
}
```

Now you can go to the tables BREAD-settings and set your field to be `Coordinates`.

After that you will get a Map where you can select your Coordinates.

{% hint style="info" %}
Make sure to set the Google Maps API-Key in your [configuration](../getting-started/configurations.md#google-maps).  
This is also the place where you can define the default location of your map.
{% endhint %}

## Getting the coordinates

You can get the coordinates from your model by calling

```php
$model->getCoordinates();
```

This will return an array of coordinates with `lat` as the latitude and `lng` as the longitude.

