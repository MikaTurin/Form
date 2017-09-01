<?php namespace Msz\Forms\Validation;

class Url extends ValidationBase
{
    protected $message = '%element% must contain a url (e.g. http://www.google.com)';

    public function isValid($value)
    {
        $is = $this->isNotApplicable($value) || filter_var($value, FILTER_VALIDATE_URL);
        return $is;
    }
}
