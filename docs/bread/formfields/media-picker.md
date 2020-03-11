# Media Picker

The media picker formfield allows you to upload/delete/select files directly from the media-manager.  
You can customize the behaviour with the following options:

```php
{
    "max": 10,
    "min": 0,
    "expanded": true,
    "show_folders": true,
    "show_toolbar": true,
    "allow_upload": true,
    "allow_move": true,
    "allow_delete": true,
    "allow_create_folder": true,
    "allow_rename": true,
    "allow_crop": true,
    "allowed": [],
    "hide_thumbnails": false,
    "quality": 90,
    "watermark": {
        "source": "...",
        "position": "top-left",
        "x": 0,
        "y": 0
    }
}
```

<table>
  <thead>
    <tr>
      <th style="text-align:left">Name</th>
      <th style="text-align:left">Description</th>
      <th style="text-align:left">Type</th>
      <th style="text-align:left">Default</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="text-align:left">base_path</td>
      <td style="text-align:left">The start path relative to the filesystem</td>
      <td style="text-align:left">String</td>
      <td style="text-align:left">/bread-slug/</td>
    </tr>
    <tr>
      <td style="text-align:left">rename</td>
      <td style="text-align:left">Rename uploaded files to a given string/expression</td>
      <td style="text-align:left">String</td>
      <td style="text-align:left">Original name</td>
    </tr>
    <tr>
      <td style="text-align:left">delete_files</td>
      <td style="text-align:left">
        <p>Delete files if the BREAD entry is deleted.</p>
        <p>Use with caution!</p>
      </td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">false</td>
    </tr>
    <tr>
      <td style="text-align:left">show_as_images</td>
      <td style="text-align:left">Shows stored data as images when browsing</td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">false</td>
    </tr>
    <tr>
      <td style="text-align:left">min</td>
      <td style="text-align:left">The minimum amount of files that can be selected</td>
      <td style="text-align:left">int</td>
      <td style="text-align:left">0</td>
    </tr>
    <tr>
      <td style="text-align:left">max</td>
      <td style="text-align:left">
        <p>The maximum amount of files that can be selected.</p>
        <p>0 means infinite</p>
      </td>
      <td style="text-align:left">int</td>
      <td style="text-align:left">0</td>
    </tr>
    <tr>
      <td style="text-align:left">expanded</td>
      <td style="text-align:left">If the media-picker should be expanded by default</td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">true</td>
    </tr>
    <tr>
      <td style="text-align:left">show_folders</td>
      <td style="text-align:left">Show subfolders</td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">true</td>
    </tr>
    <tr>
      <td style="text-align:left">show_toolbar</td>
      <td style="text-align:left">Shows/hides the whole toolbar</td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">false</td>
    </tr>
    <tr>
      <td style="text-align:left">allow_upload</td>
      <td style="text-align:left">Allow users to upload new files</td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">true</td>
    </tr>
    <tr>
      <td style="text-align:left">allow_move</td>
      <td style="text-align:left">Allow users to move files/folders</td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">true</td>
    </tr>
    <tr>
      <td style="text-align:left">allow_delete</td>
      <td style="text-align:left">Allow users to delete files</td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">true</td>
    </tr>
    <tr>
      <td style="text-align:left">allow_create_folder</td>
      <td style="text-align:left">Allow users to create new folders</td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">true</td>
    </tr>
    <tr>
      <td style="text-align:left">allow_rename</td>
      <td style="text-align:left">
        <p>Allow users to rename files.</p>
        <p>Use with caution!</p>
      </td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">true</td>
    </tr>
    <tr>
      <td style="text-align:left">allow_crop</td>
      <td style="text-align:left">Allow users to crop images</td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">true</td>
    </tr>
    <tr>
      <td style="text-align:left">hide_thumbnails</td>
      <td style="text-align:left">Hides known thumbnails and shows them as children of the parent image</td>
      <td style="text-align:left">bool</td>
      <td style="text-align:left">true</td>
    </tr>
    <tr>
      <td style="text-align:left">quality</td>
      <td style="text-align:left">Sets the quality of uploaded images and thumbnails</td>
      <td style="text-align:left">int</td>
      <td style="text-align:left">90</td>
    </tr>
    <tr>
      <td style="text-align:left">allowed</td>
      <td style="text-align:left">
        <p>The allowed types to be uploaded/selected.</p>
        <p>Empty object means all types.</p>
        <p>Files with other types won&apos;t be displayed.</p>
      </td>
      <td style="text-align:left">Object</td>
      <td style="text-align:left">[]</td>
    </tr>
  </tbody>
</table>

### Allowed types

If you want your users to only be able to upload specific file-types you can do so by passing an object with mime-types to the \`allowed\` prop, for example:

```text
["image", "audio", "video"]
```

or

```text
["image/jpeg", "image/png", "image/bmp"]
```

### Expressions

The `base_path` and `rename` can contain the following placeholders:

* `{pk}` the primary-key of the entry \(only for `base_path`\)
* `{uid}` the user-id of the current logged-in user
* `{date:format}` the current date in the format defined in `format`. For example `{date:d.m.Y}`
* `{random:10}` random string with N characters

So a `base_path` can, for example, look like the following:

```text
{
    "base_path": "/my-bread/{pk}/{date:Y}/{date:m}/"
}
```

### Watermark
A watermark can be added to uploaded images. To do so, you need to define a `source` property relative to Voyagers storage-disk.
There are a few optional arguments you can use:  
**position** the position where the watermark is placed. Can be:
- top-left (default)
- top
- top-right
- left
- center
- right
- bottom-left
- bottom
- bottom-right

**x** Relative offset to the position on the x-axis. Defaults to 0

**y** Relative offset to the position on the y-axis. Defaults to 0

**size** the size (in percent) of the watermark relative to the image. Defaults to 15

### Thumbnails
You can generate thumbnails for each uploaded image.  
A thumbnail can be one of three types:
#### Fit
Fit combines cropping and resizing to find the best way to generate a thumbnail matching your dimensions.  
You have to pass `width` and can pass `height` and `position`.  
An example for `fit` would be:
```
{
    "thumbnails": [
        {
            "type": "fit",
            "name": "fit-500",
            "width": 500, // Required
            "height": 500, // Optional
            "position": "center" // Optional. Refer to http://image.intervention.io/api/fit
        }
    ]
}
```
#### Crop
Crop an image by given dimensions and position.
You have to pass `width` and `height` and can pass `x` and `y`.  
An example for `crop` would be:
```
{
    "thumbnails": [
        {
            "type": "crop",
            "name": "crop-500-500",
            "width": 500, // Required
            "height": 500, // Required
            "x": 50, // Optional. Left offset
            "y": 50, // Optional. Top offset
        }
    ]
}
```

#### Resize
Resize the image to the given dimensions.
You have to pass `width` and/or `height`.  
Some examples for `resize`:
```
{
    "thumbnails": [
        // Width will be 500px, height will be calculated based on the aspect-ratio
        {
            "type": "resize",
            "name": "resize-500",
            "width": 500,
            "upsize": true, // Optional. Set to false to prevent upsizing
        },
        // Resizes the image to 500x500px
        {
            "type": "resize",
            "name": "resize-500-500",
            "width": 500,
            "height": 500
        },
        // Height will be 500px, width will be auto-calculated
        {
            "type": "resize",
            "name": "resize-500",
            "width": null,
            "height": 500
        }
    ]
}
```

A watermark can also be inserted into each thumbnail. Just define the [watermark-options](#watermark) on the parent and set `watermark` to `true` for each thumbnail you want to insert the watermark to.
