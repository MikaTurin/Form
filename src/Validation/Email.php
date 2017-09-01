<?php namespace Msz\Forms\Validation;

class Email extends ValidationBase
{
    protected $message = "%element% must contain correct email address";

    public function isValid($value)
    {
        $is = $this->isNotApplicable($value)
            || (filter_var($value, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $value));

        return $is;
    }
}
