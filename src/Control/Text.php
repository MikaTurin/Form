<?php namespace Msz\Form\Control;

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

        $extra = $this->generateExtra(array(
            'maxlength' => $this->maxlength,
            'class'     => $this->class
        ));

        $type = 'text';
        if ($this->isPassword) $type = 'password';

        return
            '<input type="' . $type . '" ' .
            'name="' . $this->name . '" ' .
            'value="' . htmlspecialchars($this->value) . '"' .
            $extra .
            '>';
    }
}