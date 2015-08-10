<?php namespace Msz\Forms;

class Form
{
    protected $name;
    protected $action;
    protected $method = 'POST';
    /** @var Control\Base[] */
    protected $fields;
    protected $showValues = false;
    protected $enctype = '';
    protected $errors = array();
    protected $rules = array();
    protected $tagExtra = '';
    protected $cellspacing = 0;
    protected $classname = '';
    protected $tableWidth = '';
    protected $wasProcess = false;

    function __construct($name, $action = null, $method = null)
    {
        $this->fields = array();
        $this->name = $name;
        if (!is_null($action)) {
            $this->action = basename($_SERVER['PHP_SELF']);
        }
        if (!is_null($method)) {
            $this->method = $method;
        }
    }

    public static function make($name, $action = null, $method = null)
    {
        return new static($name, $action, $method);
    }

    public function destroy()
    {
        foreach ($this->fields as $field) {
            $this->fields[$field->getName()]->destroy();
        }
    }

    public function addControl(Control\Base $obj)
    {
        $this->fields[$obj->getName()] = $obj;

        return $this;
    }

    public function setMethod($method)
    {
        if (!in_array(strtoupper($method), array('GET', 'POST'))) die('incorrect method set');
        $this->method = $method;

        return $this;
    }

    public function setCellspacing($val)
    {
        $this->cellspacing = $val;

        return $this;
    }

    /**
     * @param $name
     * @return Control\Base
     * @throws \Exception
     */
    public function getField($name)
    {
        if (!array_key_exists($name, $this->fields)) throw new \Exception('no such field: '.$name);

        return $this->fields[$name];
    }

    public function setRule($key, $arr, $condition = 'AND', $inverse = 0)
    {
        $this->rules[$key] = array('rule' => $arr, 'condition' => $condition, 'inverse' => $inverse);
    }

    public function htmlSubmit($button_text, $class = '', $extra = '', $type = 1)
    {
        if (strlen($class)) {
            $class = ' class="' . $class . '"';
        }
        if (strlen($extra)) {
            $extra = ' ' . $extra;
        }

        $ret = '';

        if ($type == 3) {
            $ret .=
                '<input type="image" border="0" alt="' . $button_text . '"' . $extra . '>';
        } elseif ($type == 2) {
            $ret .=
                '<button' . $class . $extra . '>' . $button_text . '</button>';
        } else {
            $ret .=
                '<input type="submit" value="' . $button_text . '"' . $class . $extra . '>';
        }


        return $ret;
    }

    public function drawSubmit($button_text, $class = '', $extra = '', $type = 1)
    {
        echo $this->htmlSubmit($button_text, $class, $extra, $type);
    }

    public function begin()
    {
        $enctype = $add = '';
        if (strlen($this->enctype)) {
            $enctype = ' enctype="' . $this->enctype . '"';
        }
        if (strlen($this->tagExtra)) {
            $add = ' ' . $this->tagExtra;
        }
        return '<form name="' . $this->name . '" action="' . $this->action . '" method="' . $this->method . '"' . $enctype . $add . '>';
    }

    public function end()
    {
        return
            '<input type="hidden" name="' . $this->name . '_myfrm_sbm" value="1">' .
            '</form>';
    }

    public function html($border = 0, $hide_submit = false, $hide_labels = false)
    {
        if (strlen($this->tableWidth)) {
            $ww = ' width="' . $this->tableWidth . '"';
        } else {
            $ww = '';
        }

        $ret = $this->begin() .
            '<table cellspacing="' . $this->cellspacing . '" cellpadding="0" border="' . $border . '"' . $ww . '>';

        foreach ($this->fields as $field) {
            $label = '';
            $name = $field->getName();
            if (strlen($field->getLabel())) {
                $name = $field->getLabel();
            }
            if (!$hide_labels) {
                $label = '<td valign="top" class="' . $this->classname . '">' . $name . ' </td>';
            }

            $ret .=
                '<tr>' . $label .
                '<td' . $ww . '>' . $field->html() . '</td>' .
                '</tr>';
        }

        if (!$this->showValues && !$hide_submit) {
            $ret .= '<tr><td colspan="2" align="right"><br>';
            $ret .= $this->htmlSubmit('Submit');
            $ret .= '</td></tr>' . "\n";
        }

        $ret .=
            '</table>' . "\n" . $this->end() . "\n";

        return $ret;
    }

    public function html2()
    {
        $s = $this->begin();

        array_walk($this->fields, function ($el) use (&$s) {
            $s .= $el->html();
        });
        $s .= '<input type="button" value="submit" onclick="this.form.submit();">';

        $s .= '</form><br>';

        return $s;
    }

    public function draw($border = 0)
    {
        echo $this->html($border);
    }

    public function drawValue()
    {
        $this->showValues = true;
        foreach ($this->fields as $field) {
            $this->fields[$field->getName()]->draw_value = true;
        }
    }

    public function drawHtml()
    {
        $this->showValues = false;
        foreach ($this->fields as $field) {
            $this->fields[$field->getName()]->draw_value = false;
        }
    }

    public function isSubmited()
    {
        return isset($_REQUEST[$this->name . '_myfrm_sbm']);
    }

    public function process($force = false)
    {
        if ($this->wasProcess && $force == false) {
            return true;
        }

        if (!isset($_REQUEST[$this->name . '_myfrm_sbm'])) {
            return false;
        }

        $this->errors = array();
        foreach ($this->fields as $key => $field) {
            $this->fields[$key]->process();
            if ($this->fields[$key]->getPreg()) {
                if (!preg_match($this->fields[$key]->getPreg(), $this->fields[$key]->getValue())) {
                    $this->errors[] = $field->getName();
                }
            }
        }

        $this->checkRules();
        if (sizeof($this->errors)) {
            return false;
        }
        $this->wasProcess = true;
        return true;
    }

    public function checkRules()
    {
        foreach ($this->rules as $key => $arr) {
            if (!is_array($arr['rule'])) {
                $arr['rule'] = array();
            }
            $arr['results'] = array();

            $cond = '||';
            if ($arr['condition']) {
                $cond = '&&';
            }

            $str = '';

            foreach ($arr['rule'] as $field => $val) {
                $str .= preg_match($val, $this->fields[$field]->getValue()) . ' ' . $cond . ' ';
            }
            $str = substr($str, 0, -4);
            eval ('$res = (' . $str . ');');
            if ($arr['inverse']) {
                $res = !$res;
            }
            if ($res) {
                $this->errors[] = $key;
            }
        }
    }

    public function clear()
    {
        foreach ($this->fields as $field) {
            $this->fields[$field->getName()]->setValue(null);
        }
    }

    public function getValue($name)
    {
        return $this->fields[$name]->getValue();
    }

    public function getValues($skipEmpty = false)
    {
        $r = array();
        foreach ($this->fields as $field) {
            if ($skipEmpty && !$field->getValue()) continue;
            $r[$field->getName()] = $field->getValue();
        }
        return $r;
    }
	    
    public function getErrors() 
    {
        return $this->errors;
    }	
}