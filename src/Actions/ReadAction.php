<?php

namespace TCG\Voyager\Actions;

class ReadAction extends BaseAction
{
    public function getTitle()
    {
        return __('voyager::generic.read');
    }

    public function getUrl($key)
    {
        return route('voyager.'.$this->bread->slug.'.show', $key);
    }

    public function getClasses()
    {
        return 'voyager-button blue small';
    }
}
