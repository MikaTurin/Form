<?php namespace Msz\Forms\Control;

class Select extends Base
{
    protected $options;
    protected $optionsExtra;

    public function loadArray(array $r)
    {
        $this->options = $r;

        return $this;
    }

    function html()
    {
        if ($this->drawValue) {
            $cnt = sizeof($this->options);
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
}