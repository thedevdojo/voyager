<?php

namespace TCG\Voyager\Actions;

class EditAction extends BaseAction
{
    public function getTitle()
    {
        return 'Edit';
    }

    public function getUrl($key)
    {
        return route('voyager.'.$this->bread->slug.'.edit', $key);
    }

    public function getClasses()
    {
        return 'text-white bg-yellow-500';
    }
}
