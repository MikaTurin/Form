<?php namespace Msz\Forms\Control;

class Text extends Base
{
    protected $maxlength;
    protected $isPassword = false;

    public function getMaxLength()
    {
        return $this->maxlength;
    }

    public function setMaxLength($maxlength)
    {
        $this->maxlength = $maxlength;
    }

    public function html()
    {
        if ($this->drawValue) {
            return $this->htmlValue();
        }

        $type = 'text';
        if ($this->isPassword) $type = 'password';

        $extra = $this->generateExtra(array(
            'type' => $type,
            'name' => $this->name,
            'value' => htmlspecialchars($this->value),
            'maxlength' => $this->maxlength,
            'class'     => $this->class
        ));

        return '<input type="' . $type . '" ' . $extra . '>';
    }
}