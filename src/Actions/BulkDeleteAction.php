<?php

namespace TCG\Voyager\Actions;

class BulkDeleteAction extends BaseAction
{
    public $massAction = true;
    public $method = 'delete';

    public function getTitle()
    {
        return __('voyager::bread.delete_num_entries', ['num' => ':num:', 'display_name' => ':display_name:']);
    }

    public function getClasses()
    {
        return 'text-white bg-red-500';
    }

    public function getUrl($key)
    {
        return route('voyager.'.$this->bread->slug.'.destroy', $key);
    }

    public function confirm()
    {
        return [
            'title' => __('voyager::bread.delete_num_entries', ['num' => ':num:', 'display_name' => ':display_name:']),
            'text'  => __('voyager::bread.delete_num_entries_conf', ['num' => ':num:', 'display_name' => ':display_name:']),
            'yes'   => __('voyager::generic.yes'),
            'no'    => __('voyager::generic.no'),
        ];
    }
}
