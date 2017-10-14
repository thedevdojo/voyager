<?php

namespace TCG\Voyager\Events;

use Illuminate\Queue\SerializesModels;

class FormFieldsRegistered
{
    use SerializesModels;

    public $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;

        // Deprecate on v1.3
        //
        event('voyager.form-fields.registered', $fields);
    }
}
