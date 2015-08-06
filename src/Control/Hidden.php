<?php namespace Msz\Forms\Control;

class Hidden extends Base
{
    public function html()
    {
        return '<input type="hidden" name="'.$this->name.'" value="'.htmlspecialchars($this->value).'">';
    }
}