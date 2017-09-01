<?php namespace Msz\Forms\Validation;

class ValidationClosure extends ValidationBase
{
    protected $message = '%element% callable validation failed';
    protected $closure;

    public function __construct(\Closure $closure, $message = '')
    {
        $this->closure = $closure;
        parent::__construct($message);
    }
    
    public function isValid($value)
    {
        return call_user_func($this->closure, $value);
    }
}