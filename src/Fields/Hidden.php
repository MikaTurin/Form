<?php namespace Msz\Forms\Fields;

class Hidden extends Field
{
    public function __construct($name, array $attributes = null)
    {
        parent::__construct($name, $attributes);
        $this->setAttribute('type', 'hidden');
    }
}