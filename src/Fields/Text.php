<?php namespace Msz\Forms\Fields;

use Msz\Forms\Exception;

class Text extends Field
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