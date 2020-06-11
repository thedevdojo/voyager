<?php

namespace TCG\Voyager\Actions;

class ViewAction extends AbstractAction
{
    public function getTitle()
    {
        return __('voyager::generic.view');
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
            'class' => 'btn btn-sm btn-warning pull-right view',
        ];
    }

    public function getDefaultRoute()
    {
        $id = $this->data->{$this->data->getKeyName()};

        if (is_null($id)) {
            throw new \RuntimeException('Primary key in ' . $this->dataType->model_name . ' CANNOT BE null');
        }

        return route('voyager.' . $this->dataType->slug . '.show', $id);
    }
}
