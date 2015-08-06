<?php namespace Msz\Form\Control;

class Select extends Base
{
    protected $captions;
    protected $values;
    protected $optionsExtra;

    public function loadArray($r, $emptyArray = true)
    {
        if (!is_array($r)) {
            return false;
        }
        if ($emptyArray) {
            $this->values = $this->captions = array();
        }
        foreach ($r as $k => $v) {
            $this->values[] = $k;
            $this->captions[] = $v;
        }
        return $this;
    }

    function html()
    {
        if ($this->draw_value) {
            $cnt = sizeof($this->values);
            for ($i = 0; $i < $cnt; $i++) {
                if ((string)$this->value == (string)$this->values[$i]) {
                    return $this->captions[$i];
                }
            }
            return '';
        }


        $extra = $this->generateExtra(array(
            'class' => $this->class
        ));


        $ret = '<select name="' . $this->key . '"' . $class . $extra . '>';

        $cnt = sizeof($this->values);
        for ($i = 0; $i < $cnt; $i++) {
            $selected = $extra = '';
            if ((string)$this->value == (string)$this->values[$i]) {
                $selected = ' selected="selected"';
            }
            if (isset ($this->optionsExtra[$this->values[$i]])) {
                $extra = ' ' . $this->optionsExtra[$this->values[$i]];
            }
            $ret .= '<option value="' . $this->values[$i] . '"' . $selected . $extra . '>' . $this->captions[$i] . '</option>';
        }

        $ret .= '</select>';
        return $ret;
    }
}