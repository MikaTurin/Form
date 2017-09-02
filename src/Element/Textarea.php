<?php namespace Msz\Forms\Element;

class Textarea extends ElementBase
{
    var $cols = 40;
    var $rows = 5;

    public function __construct($name, array $attributes = null)
    {
        parent::__construct($name, $attributes);
        $this
            ->setAttribute('cols', 40)
            ->setAttribute('rows', 5);
    }

    function html()
    {
        return '<textarea '.$this->getAttributesHtml('value').'>'.htmlspecialchars($this->getValue()).'</textarea>';
    }

    public function setCols($cols)
    {
        $this->setAttribute('cols', $cols);

        return $this;
    }

    public function setRows($rows)
    {
        $this->setAttribute('rows', $rows);
        
        return $this;
    }
}