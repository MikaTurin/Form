<?php namespace Msz\Forms\Validation;

class Date extends ValidationBase
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
