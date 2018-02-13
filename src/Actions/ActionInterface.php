<?php

namespace TCG\Voyager\Actions;

interface ActionInterface
{
    public function getTitle();

    public function getIcon();

    public function getAttributes();

    public function getRoute();

    public function getDataType();
}
