<?php

namespace TCG\Voyager\FormFields\After;

interface HandlerInterface
{
    public function visible($row, $dataType, $dataTypeContent, $options);

    public function handle($row, $dataType, $dataTypeContent);

    public function getCodename();

    public function createContent($row, $dataType, $dataTypeContent, $options);
}
