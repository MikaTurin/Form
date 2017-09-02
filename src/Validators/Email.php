<?php namespace Msz\Forms\Validators;

class Email extends Validator
{
    public function __construct($message = '')
    {
        $this->message = '%element% must contain correct email address';
        parent::__construct($message);
    }

    public function isValid($value)
    {
        $is = $this->isNotApplicable($value)
            || (filter_var($value, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $value));

        return $is;
    }
}
