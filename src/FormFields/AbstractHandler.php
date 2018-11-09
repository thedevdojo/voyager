<?php

namespace TCG\Voyager\FormFields;

use TCG\Voyager\Traits\Renderable;

abstract class AbstractHandler implements HandlerInterface
{
    use Renderable;

    protected $name;
    protected $codename;
    protected $supports = [];

    public function handle($row, $dataType, $dataTypeContent, $action)
    {
        $content = $this->createContent(
            $row,
            $dataType,
            $dataTypeContent,
            $row->details,
            $action
        );

        return $this->render($content);
    }

    protected function getViewFileList($dataType, $action)
    {
        return [
            "voyager::{$dataType->slug}.formfields.{$this->codename}.{$action}",
            "voyager::formfields.{$this->codename}.{$action}",
            "voyager::{$dataType->slug}.formfields.{$this->codename}",
            "voyager::formfields.{$this->codename}",
        ];
    }

    public function createContent($row, $dataType, $dataTypeContent, $options, $action)
    {
        return view()->first($this->getViewFileList($dataType, $action), [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
            'action'          => $action,
        ]);
    }

    public function supports($driver)
    {
        if (empty($this->supports)) {
            return true;
        }

        return in_array($driver, $this->supports);
    }

    public function getCodename()
    {
        if (empty($this->codename)) {
            $name = class_basename($this);

            if (ends_with($name, 'Handler')) {
                $name = substr($name, 0, -strlen('Handler'));
            }

            $this->codename = snake_case($name);
        }

        return $this->codename;
    }

    public function getName()
    {
        if (empty($this->name)) {
            $this->name = ucwords(str_replace('_', ' ', $this->getCodename()));
        }

        return $this->name;
    }
}
