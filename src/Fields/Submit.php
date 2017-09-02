<?php namespace Msz\Forms\Fields;

class Submit extends Field
{
    public function __construct($name, array $attributes = null)
    {
        parent::__construct($name, $attributes);
        $this->position = Field::POSITION_BUTTON;
        $this->setAttribute('type', 'submit');
    }

    public function isUsed()
    {
        return (isset($_REQUEST[$this->getName()]) && $_REQUEST[$this->getName()] === $this->getValue());
    }
}