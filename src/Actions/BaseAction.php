<?php

namespace TCG\Voyager\Actions;

abstract class BaseAction implements \JsonSerializable
{
    public $bread;

    public $massAction = false;
    public $method = 'get';

    abstract public function getTitle();

    abstract public function getClasses();

    public function getUrl($key)
    {
        return '#';
    }

    public function getParameter()
    {
        return [];
    }

    public function shouldBeDisplayOnBread()
    {
        return true;
    }

    public function shouldBeDisplayedOnEntry($entry)
    {
        return true;
    }

    public function confirm()
    {
        return false;
    }

    public function jsonSerialize()
    {
        return [
            'title'      => $this->getTitle(':num:'),
            'classes'    => $this->getClasses(),
            'url'        => $this->getUrl(':key:'),
            'parameter'  => $this->getParameter(),
            'confirm'    => $this->confirm(),
            'method'     => $this->method,
            'massAction' => $this->massAction,
        ];
    }
}
