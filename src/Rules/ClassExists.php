<?php

namespace TCG\Voyager\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ClassExists implements Rule
{
    protected $value;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;

        return class_exists(Str::start($value, '\\'));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('voyager::validation.class_does_not_exist', ['value' => $this->value]);
    }
}
