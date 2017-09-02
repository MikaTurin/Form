<?php namespace Msz\Forms\Validators;

class Date extends Validator
{
    public function __construct($message = '')
    {
        $this->message = '%element% must contain a valid date';
        parent::__construct($message);
    }

    public function isValid($value)
    {
        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
