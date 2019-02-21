<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

class Relationship extends BaseType
{
    /**
     * @return array
     */
    public function handle()
    {
        $content = $this->request->input($this->row->field);
        if (is_array($content)) {
            for ($i = 0; $i < count($content); $i++) {
                if ($content[$i] === null) {
                    unset($content[$i]);
                }
            }
        }

        return $content;
    }
}
