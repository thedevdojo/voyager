<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

class TwoColumnsList extends BaseType
{
    /**
     * @return string
     */
    public function handle()
    {
        return $this->request->input($this->row->field) ?? '{}';
    }
}
