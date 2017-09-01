<?php namespace Msz\Forms;

class Base
{
    protected $attributes;


    public function filter($s)
    {
        return htmlspecialchars($s);
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

    public function getAttributes($ignore = null)
    {
        if (empty($this->attributes)) {
            return '';
        }

        $s = '';

        if (!empty($ignore) && !is_array($ignore)) {
            $ignore = array($ignore);
        }
        elseif (empty($ignore)) {
            $ignore = array();
        }

        $attributes = array_diff(array_keys($this->attributes), $ignore);

        foreach ($attributes as $att) {
            $s .= ' ' . $att;
            if ($this->attributes[$att] !== '') {
                $s .= '="' . $this->filter($this->attributes[$att]) . '"';
            }
        }

        return $s;
    }
}