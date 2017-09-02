<?php namespace Msz\Forms\Validator;

class Email extends ValidatorBase
{
    protected $message = "%element% must contain correct email address";

    public function isValid($value)
    {
        $is = $this->isNotApplicable($value)
            || (filter_var($value, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $value));

        return $is;
    }
}
