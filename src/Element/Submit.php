<?php namespace Msz\Forms\Element;

class Submit extends ElementBase
{
    public function __construct($name, $class = null, $value = null)
    {
        parent::__construct($name, $class, $value);
        $this->type = ElementBase::BUTTON;
    }

    public function html()
    {
        $extra = $this->generateExtra(array(
            'name' => $this->name,
            'value' => $this->value
        ));
        return '<input type="submit"'. $extra . '>';
    }

    public function isUsed()
    {
        return (isset($_REQUEST[$this->name]) && $_REQUEST[$this->name] == $this->value);
    }
}