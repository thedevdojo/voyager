<?php

namespace TCG\Voyager\FormFields;

interface HandlerInterface
{
    public function handle($row, $dataType, $dataTypeContent, $action);

    public function createContent($row, $dataType, $dataTypeContent, $options, $action);

    public function supports($driver);

    public function getCodename();

    public function getName();
}
