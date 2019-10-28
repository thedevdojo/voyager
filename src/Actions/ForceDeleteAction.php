<?php

namespace TCG\Voyager\Actions;

class ForceDeleteAction extends BaseAction
{
    public $method = 'delete';

    public function getTitle()
    {
        return __('voyager::bread.force_delete');
    }

    public function getClasses()
    {
        return 'button red small';
    }

    public function getUrl($key)
    {
        return route('voyager.'.$this->bread->slug.'.delete', $key);
    }

    public function getParameter()
    {
        return [
            'force' => true,
        ];
    }

    public function confirm()
    {
        return [
            'title' => __('voyager::bread.force_delete_entry', ['display_name' => ':display_name:']),
            'text'  => __('voyager::bread.force_delete_entry_conf', ['display_name' => ':display_name:']),
            'yes'   => __('voyager::generic.yes'),
            'no'    => __('voyager::generic.no'),
        ];
    }

    public function shouldBeDisplayOnBread()
    {
        return $this->layout->force_delete ?? false;
    }

    public function shouldBeDisplayedOnEntry($entry)
    {
        if ($this->bread->usesSoftDeletes()) {
            return $entry->trashed() && $this->layout->force_delete ?? false;
        }

        return false;
    }
}
