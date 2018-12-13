<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

class KeyValueJson extends BaseType
{
    /**
     * @return string|\Illuminate\Database\Query\Expression
     */
    public function handle()
    {
        if (empty($key_value_json = $this->request->input($this->row->field))) {
            return;
        }

        $new_parameters = [];
        foreach ($key_value_json as $key => $value) {
            if ($key_value_json[$key]['key']) {
                $new_parameters[$key_value_json[$key]['key']] = $key_value_json[$key]['value'];
            }
        }

        return json_encode($new_parameters);
    }
}
