# BREAD accessors

Sometimes you want to format an attribute only for one \(or some\) of the BREAD-actions.  
For example if you have a `name` field and on the browse-page you want to display something when the field is empty, you define the following in your model:

```php
<?php

public function getNameBrowseAttribute()
{
    return $this->name ?? 'Empty';
}
```

This will display "Empty" if the actual field is empty, or return the value if not.

Likewise you can do the same for the other BREAD-actions:

```php
<?php

public function getNameReadAttribute()
{
    //
}

public function getNameEditAttribute()
{
    //
}

public function getNameAddAttribute()
{
    //
}
```

