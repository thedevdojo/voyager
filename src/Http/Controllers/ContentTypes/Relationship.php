<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

class Relationship extends BaseType
{
    /**
     * @return string
     */
    public function handle()
    {
        $content = $this->request->input($this->row->field);
        for ($i=0; $i<sizeof($content); $i++) {
            if ($content[$i] === null) {
                unset($content[$i]);
            }
        }
        return $content;
    }
}
