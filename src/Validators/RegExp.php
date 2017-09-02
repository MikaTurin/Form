<?php namespace Msz\Forms\Validators;

class RegExp extends Validator
{
    protected $pattern;

    public function __construct($pattern, $message = '')
    {
        $this->message = '%element% failed on regexp';
        $this->pattern = $pattern;
        parent::__construct($message);
    }

    public function isValid($value)
    {
        return $this->isNotApplicable($value) || preg_match($this->pattern, $value);
    }
}
