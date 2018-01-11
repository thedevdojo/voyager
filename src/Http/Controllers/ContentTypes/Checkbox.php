<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

class Checkbox extends BaseType
{
    /**
     * @return integer
     */
    public function handle()
    {
        return (int) $this->request->input($this->row->field, 0);
    }
}