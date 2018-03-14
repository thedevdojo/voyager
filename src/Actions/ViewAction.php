<?php

namespace TCG\Voyager\Actions;

class ViewAction extends AbstractAction
{
    public function getTitle()
    {
        return __('voyager::voyager.generic.view');
    }

    public function getIcon()
    {
        return 'voyager-eye';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-warning pull-right edit',
        ];
    }

    public function getDefaultRoute()
    {
        return route('voyager.'.$this->dataType->slug.'.show', $this->data->{$this->data->getKeyName()});
    }
}
