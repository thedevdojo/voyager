<?php

namespace TCG\Voyager\FormFields;

use TCG\Voyager\Traits\Renderable;

abstract class AbstractHandler implements HandlerInterface
{
    use Renderable;

    protected $name;
    protected $codename;
    protected $supports = [];

    public function handle($row, $dataType, $dataTypeContent)
    {
        $content = $this->createContent(
            $row,
            $dataType,
            $dataTypeContent,
            $row->details
        );

        return $this->render($content);
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
