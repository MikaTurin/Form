<?php namespace Msz\Forms\Element;

class Hidden extends ElementBase
{
    public function __construct($name, array $attributes = null)
    {
        parent::__construct($name, $attributes);
        $this->setAttribute('type', 'hidden');
    }
}