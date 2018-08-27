<?php

namespace TCG\Voyager\FormFields\After;

class DescriptionHandler extends AbstractHandler
{
    protected $codename = 'description';

    public function visible($row, $dataType, $dataTypeContent, $options)
    {
        $trans = 'voyager.help.'.$dataType->name.'.'.$row->field;
        if(__($trans) !== $trans) {
            return true;
        }

        if (!isset($options->description)) {
            return false;
        }

        return !empty($options->description);
    }

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        if(isset($options->description)) {
            $helpText = $options->description;
        } else {
            $helpText = __('voyager.help.'.$dataType->name.'.'.$row->field);
        }

        return '<span class="glyphicon glyphicon-question-sign"
                                        aria-hidden="true"
                                        data-toggle="tooltip"
                                        data-placement="auto"
                                        data-html="true"
                                        title="'.$helpText.'"></span>';
    }
}
