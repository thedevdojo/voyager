<?php

namespace TCG\Voyager\Rules;

use Illuminate\Contracts\Validation\Rule;
use TCG\Voyager\Facades\Voyager;

class DefaultLocale implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_string($value) || !Voyager::isTranslatable()) {
            return !empty($value);
        }

        return !empty($value[Voyager::getLocale()]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('voyager::validation.default_translation');
    }
}
