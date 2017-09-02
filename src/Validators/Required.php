<?php namespace Msz\Forms\Validators;

class Required extends Validator
{
    public function __construct($message = '')
    {
        $this->message = '%element% is a required field';
        parent::__construct($message);
    }

    public function isValid($value)
    {
        return (null !== $value) && ((!is_array($value) && $value !== '') || (is_array($value) && count($value)));
    }
}
