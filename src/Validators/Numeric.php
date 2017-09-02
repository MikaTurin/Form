<?php namespace Msz\Forms\Validators;

class Numeric extends Validator
{
    public function __construct($message = '')
    {
        $this->message = '%element% must be numeric';
        parent::__construct($message);
    }

    public function isValid($value)
    {
        return $this->isNotApplicable($value) || is_numeric($value);
    }
}
