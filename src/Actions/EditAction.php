<?php

namespace TCG\Voyager\Actions;

class EditAction extends BaseAction
{
    public function getTitle()
    {
        return __('voyager::generic.edit');
    }

    public function getUrl($key)
    {
        return route('voyager.'.$this->bread->slug.'.edit', $key);
    }

    public function getClasses()
    {
        return 'voyager-button yellow small';
    }
}
