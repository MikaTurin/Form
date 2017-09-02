<?php namespace Msz\Forms\Validator;

class Numeric extends ValidatorBase
{
    protected $message = '%element% must be numeric';

    public function isValid($value)
    {
        $is = $this->isNotApplicable($value) || is_numeric($value);
        return $is;
    }
}
