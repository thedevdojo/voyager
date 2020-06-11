<?php

namespace TCG\Voyager\Actions;

class EditAction extends AbstractAction
{
    public function getTitle()
    {
        return __('voyager::generic.edit');
    }

    public function getIcon()
    {
        return 'voyager-edit';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary pull-right edit',
        ];
    }

    public function getDefaultRoute()
    {
        $id = $this->data->{$this->data->getKeyName()};

        if (is_null($id)) {
            throw new \RuntimeException('Primary key in ' . $this->dataType->model_name . ' CANNOT BE null');
        }
        
        return route('voyager.'.$this->dataType->slug.'.edit', $id);
    }
}
