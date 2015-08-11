<?php namespace Msz\Forms\Control;

class Select extends Base
{
    protected $options;
    protected $optionsExtra;
    protected $useFirstEmpty = null;

    public function loadArray(array $r)
    {
        $this->options = $r;

        return $this;
    }

    function html()
    {
        if ($this->drawValue) {

            foreach ($this->options as $k => $v) {
                if ((string)$this->value == (string)$k) {
                    return $v;
                }
            }
            return '';
        }


        $extra = $this->generateExtra(array(
            'name' => $this->name,
            'class' => $this->class
        ));

        $ret = '<select' . $extra . '>';

        if (!is_null($this->useFirstEmpty)) {
            $ret .= '<option value="">'.$this->useFirstEmpty.'</option>';
        }

        foreach ($this->options as $k => $v) {
            $selected = $extra = '';
            if ((string)$this->value == (string)$k) {
                $selected = ' selected="selected"';
            }
            if (isset($this->optionsExtra[$k])) {
                $extra = ' ' . $this->optionsExtra[$k];
            }
            $ret .= '<option value="' . $k . '"' . $selected . $extra . '>' . $v . '</option>';
        }

        $ret .= '</select>';
        return $ret;
    }

    public function setUseFirstEmpty($useFirstEmpty = '')
    {
        $this->useFirstEmpty = $useFirstEmpty;

        return $this;
    }

    public function setVerifyValueWithOptions()
    {
        $this->setVerifier(function ($value) {
            if (!array_key_exists($value, $this->options)) {
                return array('field' => $this->name, 'message' => 'value not in the list');
            }
            return null;
        });

        return $this;
    }
}