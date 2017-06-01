<?php namespace Msz\Forms\Control;

class Submit extends Base
{
    public function __construct($name, $class = null, $value = null)
    {
        parent::__construct($name, $class, $value);
        $this->type = Base::BUTTON;
    }

    public function html()
    {
        $extra = $this->generateExtra(array(
            'name' => $this->name,
            'value' => $this->value
        ));
        return '<input type="submit"'. $extra . '>';
    }
}