<?php namespace Msz\Forms\Element;

use Msz\Forms\Exception;

class Text extends ElementBase
{
    public function __construct($name, array $attributes = null)
    {
        parent::__construct($name, $attributes);
        $this->setAttribute('type', 'text');
    }

    public function getMaxLength()
    {
        return $this->getAttribute('maxlength');
    }

    public function setMaxLength($maxlength)
    {
        $maxlength = (int)$maxlength;
        if (!$maxlength) {
            throw new Exception('maxlength cant be zero');
        }
        $this->setAttribute('maxlength', $maxlength);
		
		return $this;
    }
}