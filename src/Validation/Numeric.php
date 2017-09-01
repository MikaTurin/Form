<?php namespace Msz\Forms\Validation;

class Numeric extends ValidationBase
{
    protected $message = '%element% must be numeric';

    public function isValid($value)
    {
        $is = $this->isNotApplicable($value) || is_numeric($value);
        return $is;
    }
}
