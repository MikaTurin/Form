<?php namespace Msz\Forms\Validation;

class AlphaNumeric extends RegExp
{
    protected $message = '%element% must be alphanumeric (contain only numbers, letters and/or hyphens)';

    public function __construct($message = '')
    {
        parent::__construct("/^[a-zA-Z0-9-]+$/", $message);
    }
}
