<?php

namespace TCG\Voyager\Events;

use Illuminate\Queue\SerializesModels;
use TCG\Voyager\Models\DataType;

class BreadUpdated
{
    use SerializesModels;

    public $dataType;

    public $data;

    public $request_data;

    public function __construct(DataType $dataType, $data, $request_data)
    {
        $this->dataType = $dataType;

        $this->data = $data;

        $this->request_data = $request_data;

        event(new BreadChanged($dataType, $data, 'Updated'));
    }
}
