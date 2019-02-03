# Adding custom Formfields

You can easily add a new Formfield to Voyager. In the example below we will add a number form field \(which is already included by default in Voyager\).

First we create a new class in our project \(it doesn't matter where it is placed\) called `NumberFormField`

```php
<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class NumberFormField extends AbstractHandler
{
    protected $codename = 'number';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.number', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}
```

The `codename` variable is used in the dropdown you see in the BREAD builder. In the `createContent` method we are returning the view that is used to display our form field.

Next, we will create the view specified above.

```markup
<input type="number"
       class="form-control"
       name="{{ $row->field }}"
       data-name="{{ $row->display_name }}"
       type="number"
       @if($row->required == 1) required @endif
             step="any"
       placeholder="{{ isset($options->placeholder)? old($row->field, $options->placeholder): $row->display_name }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif">
```

In the view we can add whatever logic we want.

When we are done with our view, we will tell Voyager that we have a new form field. We will do this in a service provider \(in the example below we use the `AppServiceProvider`.

```php
<?php

namespace App\Providers;

use TCG\Voyager\Facades\Voyager;
use App\FormFields\NumberFormField;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    //..

    public function register()
    {
        Voyager::addFormField(NumberFormField::class);
    }
}
```

