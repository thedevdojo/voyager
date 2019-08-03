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

    public function shouldBeDisplayOnBread($bread)
    {
        return true;
    }

    public function jsonSerialize()
    {
        return [
            'title'      => $this->getTitle(':num:'),
            'classes'    => $this->getClasses(),
            'url'        => $this->getUrl(':key:'),
            'method'     => $this->method,
            'massAction' => $this->massAction,
        ];
    }
}
