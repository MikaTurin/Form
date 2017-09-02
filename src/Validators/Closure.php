<?php namespace Msz\Forms\Validators;

class Closure extends Validator
{
    protected $closure;

    public function __construct(\Closure $closure, $message = '')
    {
        $this->message = '%element% callable validation failed';
        $this->closure = $closure;
        parent::__construct($message);
    }
    
    public function isValid($value)
    {
        return call_user_func($this->closure, $value);
    }
}