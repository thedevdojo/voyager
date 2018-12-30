<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

class MultipleCheckbox extends BaseType
{
    /**
     * @return mixed
     */
    public function handle()
    {
        $content = $this->request->input($this->row->field, []);
        if (true === empty($content)) {
            return json_encode([]);
        }

        return json_encode($content);
    }
}
