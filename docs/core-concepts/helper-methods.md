# Helper methods

Voyager has several helper functions that are ready to use. Here you can find the list of available function that may speed up your development.

## Thumbnails URL

Voyager will generate thumbnails for Image field type when you specify the [additional field options](https://github.com/emptynick/voyager/tree/735a22e97d81b204cc668c421aa06e1268182ed9/bread-builder?id=image/README.md).

After you have your thumbnails generated, you may want to display the thumbnails in your view or get the thumbnail URL. In order to do that you need to add `Resizable` traits to your model.

```php
use TCG\Voyager\Traits\Resizable;

class Post extends Model
{
    use Resizable;
}
```

Then you can call the thumbnail function in your view or anywhere you like.

```php
@foreach($posts as $post)
    <img src="{{Voyager::image($post->thumbnail('small'))}}" />
@endforeach
```

Or you can specify the optional image field name \(attribute\), default to `image`

```php
@foreach($posts as $post)
    <img src="{{Voyager::image($post->thumbnail('small', 'photo'))}}" />
@endforeach
```

