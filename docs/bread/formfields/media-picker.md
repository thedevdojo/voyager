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

