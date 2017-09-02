<?php namespace Msz\Forms\Validator;

abstract class ValidatorBase
{
    protected $message = "%element% is invalid.";

    public function __construct($message = "")
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
        $is = (is_null($value) || is_array($value) || $value === "");
        return $is;
    }

    public abstract function isValid($value);
}
