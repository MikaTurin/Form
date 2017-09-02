<?php namespace Msz\Forms\Fields;

class Password extends Text
{
    public function __construct($name, array $attributes = null)
    {
        parent::__construct($name, $attributes);
        $this->setAttribute('type', 'password');
    }
}