<?php namespace Msz\Forms\Validators;

abstract class Validator
{
    protected $message = '%element% is invalid';

    public function __construct($message = '')
    {
        if (!empty($message)) {
            $this->message = $message;
        }
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function isNotApplicable($value)
    {
        return (null === $value || is_array($value) || $value === '');
    }

    abstract public function isValid($value);
}
