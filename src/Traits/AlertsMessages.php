<?php

namespace TCG\Voyager\Traits;

trait AlertsMessages
{
    protected $alerts = [];

    protected function getAlerts($group = false)
    {
        if (isset($this->alerts['alerts'])) {
            $alerts = $this->alerts['alerts'];

            if ($group) {
                $alerts = collect($alerts)->groupBy('type')->toArray();
            }

            return $alerts;
        }

        return [];
    }

    protected function alert($message, $type)
    {
        $this->alerts['alerts'][] = [
            'type'    => $type,
            'message' => $message,
        ];

        return $this->alerts;
    }

    protected function alertSuccess($message)
    {
        return $this->alert($message, 'success');
    }

    protected function alertInfo($message)
    {
        return $this->alert($message, 'info');
    }

    protected function alertWarning($message)
    {
        return $this->alert($message, 'warning');
    }

    protected function alertError($message)
    {
        return $this->alert($message, 'error');
    }

    protected function alertException(\Exception $e, $prefixMessage = '')
    {
        return $this->alertError("{$prefixMessage} Exception: {$e->getMessage()}");
    }
}
