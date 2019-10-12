<?php

namespace TCG\Voyager\Actions;

class RestoreAction extends BaseAction
{
    public $method = 'delete';

    public function getTitle()
    {
        return __('voyager::generic.restore');
    }

    public function getClasses()
    {
        return 'button green small';
    }

    public function getUrl($key)
    {
        return route('voyager.'.$this->bread->slug.'.destroy', $key);
    }

    public function getParameter()
    {
        return [
            'restore' => true,
        ];
    }

    public function confirm()
    {
        return [
            'title' => __('voyager::bread.restore_single_entry', ['display_name' => ':display_name:']),
            'text'  => __('voyager::bread.restore_single_entry_conf', ['display_name' => ':display_name:']),
            'yes'   => __('voyager::generic.yes'),
            'no'    => __('voyager::generic.no'),
        ];
    }

    public function shouldBeDisplayedOnEntry($entry)
    {
        if ($this->bread->usesSoftDeletes()) {
            return $entry->trashed() && $this->bread->restore ?? false;
        }

        return false;
    }
}
