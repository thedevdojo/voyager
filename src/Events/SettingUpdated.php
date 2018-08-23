<?php

namespace TCG\Voyager\Events;

use TCG\Voyager\Models\Setting;
use Illuminate\Queue\SerializesModels;

class SettingUpdated
{
    use SerializesModels;

    public $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }
}
