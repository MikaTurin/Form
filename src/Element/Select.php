<?php namespace Msz\Forms\Element;

use Msz\Forms\Validation\ValidationClosure;

class Select extends ElementBase
{
    protected $options;
    protected $optionAttributes = array(); //array of strings, key have to match with options key
    protected $emptyFirstOption;

    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    function html()
    {
        $ret = '<select' . $this->getAttributesHtml('value') . '>';

        if (!is_null($this->emptyFirstOption)) {
            $ret .= '<option value="">'.$this->emptyFirstOption.'</option>';
        }

        foreach ($this->options as $k => $v) {
            $selected = $extra = '';
            if ((string)$this->getValue() == (string)$k) {
                $selected = ' selected="selected"';
            }
            if (isset($this->optionAttributes[$k])) {
                $extra = ' ' . $this->optionAttributes[$k];
            }
            $ret .= '<option value="' . $k . '"' . $selected . $extra . '>' . $v . '</option>';
        }

        $ret .= '</select>';
        return $ret;
    }

    public function setEmptyFirstOption($value = '')
    {
        $this->emptyFirstOption = $value;

        return $this;
    }

    public function verifyValueWithOptionsList()
    {
        $options = $this->options;
        $validation = new ValidationClosure(function ($value) use ($options) {
            return array_key_exists($value, $options);
        });

        $this->setValidation($validation->setMessage('value not in the options list'));

        return $this;
    }
}