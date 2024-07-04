<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

class SimpleList extends BaseType
{
    /**
     * @return string
     */
    public function handle()
    {
        return $this->request->input($this->row->field) ?? '{}';
    }
}
