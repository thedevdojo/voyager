# Media Picker

The media picker formfield allows you to upload/delete/select files directly from the media-manager.  
You can customize the behaviour with the following options:

```php
{
    "max": 10,
    "min": 0,
    "show_folders": true,
    "show_toolbar": true,
    "allow_upload": true,
    "allow_move": true,
    "allow_delete": true,
    "allow_create_folder": true,
    "allow_rename": true,
    "allow_crop": true,
    "allowed": []
}
```

* `base_path` the start-path relative to the filesystem.
* `rename` renames the uploaded files to a given name
* `show_as_images` shows stored data as images
* `delete_files` boolean value if the files should be deleted when the entry is deleted. This will also delete the file if it is used in other entries!
* `max` the maximum of files a user can select
* `min` the minimum of files that are required
* `show_folders` show subfolders or not
* `show_toolbar` hide the whole toolbar
* `allow_upload` allow users to upload new files
* `allow_move` let users move files
* `allow_delete` allow users to delete files
* `allow_create_folder` let users create new folders
* `allow_rename` rename files
* `allow_crop` let users crop images
* `allowed` an object of mimetypes that are displayed. For example  

  `["image", "audio", "video"]`  

  or  

  `["image/jpeg", "image/png", "image/bmp"]`

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

| Name | Description | Type |
| :--- | :--- | :--- |
| base\_path | The start path relative to the filesystem | String |

