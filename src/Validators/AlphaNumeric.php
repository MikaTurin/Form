<?php namespace Msz\Forms\Validators;

class AlphaNumeric extends RegExp
{
    public function __construct($message = '')
    {
        $this->message = '%element% must be alphanumeric (contain only numbers, letters and/or hyphens)';
        parent::__construct('/^[a-zA-Z0-9-]+$/', $message);
    }
}
