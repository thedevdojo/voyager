<?php

namespace TCG\Voyager\Actions;

class BulkDeleteAction extends BaseAction
{
    public $massAction = true;
    public $method = 'delete';

    public function getTitle()
    {
        return __('voyager::bread.delete_num_breads', ['num' => ':num:', 'display_name' => ':display_name:']);
    }

    public function getClasses()
    {
        return 'text-white bg-red-500';
    }
}