<?php namespace Msz\Forms;

class Base
{
    protected $attributes;

    
    protected static function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES);
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function getAttribute($name)
    {
        $value = null;
        if (isset($this->attributes[$name])) {
            $value = $this->attributes[$name];
        }
        return $value;
    }

    public function appendAttribute($name, $value)
    {
        if (!empty($this->attributes[$name])) {
            $this->attributes[$name] .= ' ' . $value;
        } else {
            $this->attributes[$name] = $value;
        }

        return $this;
    }

    protected static function getAttributeHtml($name, $value)
    {
        if (($value === null) || ($value === false)) {
            return '';
        }

        if ($value === true) {
            return " {$name}";
        }

        if (is_array($value)) {
            $value = implode(' ', $value);
        }

        return sprintf(' %s="%s"', $name, static::escape($value));
    }

    public function getAttributesHtml($ignore = null)
    {
        if (empty($this->attributes)) {
            return '';
        }

        $s = ' ';

        if (null === $ignore) {
            $ignore = array();
        }
        elseif (!is_array($ignore)) {
            $ignore = array($ignore);
        }

        $attributes = array_diff(array_keys($this->attributes), $ignore);

        foreach ($attributes as $name) {
            $s .= static::getAttributeHtml($name, $this->attributes[$name]);
        }

        return $s;
    }

    public function setClass($class)
    {
        $this->setAttribute('class', $class);

        return $this;
    }

    public function addClass($class)
    {
        $this->appendAttribute('class', $class);

        return $this;
    }

    public function getClass()
    {
        return $this->getAttribute('class');
    }

    public function setStyle($style)
    {
        $this->setAttribute('style', rtrim(trim($style),';') . ';');
    }

    public function addStyle($style)
    {
        $this->appendAttribute('style', rtrim(trim($style),';') . ';');

        return $this;
    }

    public function getStyle()
    {
        return $this->getAttribute('style');
    }
}