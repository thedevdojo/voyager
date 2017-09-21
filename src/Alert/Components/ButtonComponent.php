<?php

namespace TCG\Voyager\Alert\Components;

class ButtonComponent extends AbstractComponent
{
    protected $text;
    protected $link;
    protected $style;

    public function create($text, $link = '#', $style = 'default')
    {
        $this->text = $text;
        $this->link = $link;
        $this->style = $style;
    }

    public function render()
    {
        return "<a href='{$this->link}' class='btn btn-{$this->style}'>{$this->text}</a>";
    }
}
