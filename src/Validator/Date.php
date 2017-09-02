<?php namespace Msz\Forms\Validator;

class Date extends ValidatorBase
{
    protected $message = '%element% must contain a valid date';

    public function isValid($value)
    {
        try {
            $date = new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
