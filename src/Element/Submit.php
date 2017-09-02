<?php namespace Msz\Forms\Element;

class Submit extends ElementBase
{
    public function __construct($name, array $attributes = null)
    {
        parent::__construct($name, $attributes);
        $this->position = ElementBase::POSITION_BUTTON;
        $this->setAttribute('type', 'submit');
    }

    public function isUsed()
    {
        return (isset($_REQUEST[$this->getName()]) && $_REQUEST[$this->getName()] === $this->getValue());
    }
}