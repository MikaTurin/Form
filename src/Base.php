<?php namespace Msz\Forms;

class Base
{
    protected $attributes;


    protected static function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
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
            $this->attributes[$name] .= " " . $value;
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

        if (!empty($ignore) && !is_array($ignore)) {
            $ignore = array($ignore);
        }
        elseif (empty($ignore)) {
            $ignore = array();
        }

        $attributes = array_diff(array_keys($this->attributes), $ignore);

        foreach ($attributes as $name) {
            $s .= $this->getAttributeHtml($name, $this->attributes[$name]);
        }

        return $s;
    }
}