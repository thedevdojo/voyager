<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

class Timestamp extends BaseType
{
    public function handle()
    {
        if (!in_array($this->request->method(), ['PUT', 'POST'])) {
            return;
        }

        $content = $this->request->input($this->row->field);

        if (empty($content)) {
            return;
        }

        return gmdate('Y-m-d H:i:s', strtotime($content));
    }
}
