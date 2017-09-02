<?php namespace Msz\Forms\Validators;

class Url extends Validator
{
    public function __construct($message = '')
    {
        $this->message = '%element% must contain a url';
        parent::__construct($message);
    }

    public function isValid($value)
    {
        return $this->isNotApplicable($value) || filter_var($value, FILTER_VALIDATE_URL);
    }
}
