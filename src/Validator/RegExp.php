<?php namespace Msz\Forms\Validator;

class RegExp extends ValidatorBase
{
    protected $message = '%element% failed on regexp';
    protected $pattern;

    public function __construct($pattern, $message = '')
    {
        $this->pattern = $pattern;
        parent::__construct($message);
    }

    public function isValid($value)
    {
        return ($this->isNotApplicable($value) || preg_match($this->pattern, $value)) ? true : false;
    }
}
