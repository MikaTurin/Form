<?php namespace Msz\Forms\Fields;

class Textarea extends Field
{
    public function __construct($name, array $attributes = null)
    {
        parent::__construct($name, $attributes);
        $this
            ->setAttribute('cols', 40)
            ->setAttribute('rows', 5);
    }

    public function html()
    {
        return sprintf('<textarea%s>%s</textarea>', $this->getAttributesHtml('value'), static::escape($this->getValue()));
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