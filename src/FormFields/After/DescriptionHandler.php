<?php

namespace TCG\Voyager\FormFields\After;

class DescriptionHandler extends AbstractHandler
{
    protected $codename = 'description';

    public function visible($row, $dataType, $dateTypeContent, $options)
    {
        if (!isset($options->description)) {
            return false;
        }

        return !empty($options->description);
    }

    public function createContent($row, $dataType, $dateTypeContent, $options)
    {
        return '<span class="glyphicon glyphicon-question-sign"
                                        aria-hidden="true"
                                        data-toggle="tooltip"
                                        data-placement="right"
                                        title="'.$options->description.'"></span>';
    }
}
