<?php namespace Msz;

class Form
{
    protected $name;
    protected $action;
    protected $method = 'POST';
    /** @var array  */
    protected $fields;
    protected $showValues = false;
    protected $enctype = '';
    protected $errors = array();
    protected $rules = array();
    protected $tag_extra = '';
    protected $cellspacing = 0;
    protected $classname = '';
    protected $tableWidth = '';
    protected $wasProcess = false;

    function __construct($name = 'frm', $action = "")
    {
        $this->fields = array();
        $this->name = $name;
        if (!strlen($action)) {
            $action = basename($_SERVER['PHP_SELF']);
        }
        $this->action = $action;
    }

    public static function make($name = 'frm', $action = "")
    {
        return new static($name, $action);
    }

    function destroy()
    {
        foreach ($this->fields as $field) {
            $this->fields[$field->name]->destroy();
        }
    }

    /**
     * @param $control
     * @param $name
     * @param null $param1
     * @param null $param2
     * @param null $param3
     * @param null $param4
     * @param null $param5
     * @param null $param6
     * @return myform_control
     */
    function add_control(
        $control,
        $name,
        $param1 = null,
        $param2 = null,
        $param3 = null,
        $param4 = null,
        $param5 = null,
        $param6 = null
    ) {
        $str = '';
        for ($i = 1; $i <= 6; $i++) {
            $var = 'param' . $i;
            if (!($$var === null)) {
                $str .= ", \$$var";
            } else {
                break;
            }
        }
        $str = "\$this->fields['$name'] = new myform_$control (\$this->name, '$name' $str);";
        eval ($str);
        if ($control == 'xbeditor') {
            $this->tag_extra = ' onsubmit="return xbUpdateField(this.' . $name . ');"';
        }

        return $this->fields[$name];
    }

    public function addControl(myform_control $obj)
    {
        $this->fields[$obj->name] = $obj->setFormName($this->name);

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

    function set_label($key, $label)
    {
        if (isset ($this->fields[$key])) {
            $this->fields[$key]->label = $label;
        }
    }

    /**
     * @param $name
     * @return \myform_control
     * @throws Exception
     */
    function getField($name)
    {
        if (!array_key_exists($name, $this->fields)) throw new \Exception('no such field: '.$name);

        return $this->fields[$name];
    }

    function setLabel($key, $label)
    {
        $this->set_label($key, $label);
    }

    function set_preg($key, $preg)
    {
        $this->setPreg($key, $preg);
    }

    function setPreg($key, $preg)
    {
        if (isset ($this->fields[$key])) {
            $this->fields[$key]->preg_check = $preg;
        }
    }

    function set_rule($key, $arr, $condition = MYFORM_AND, $inverse = 0)
    {
        $this->rules[$key] = array('rule' => $arr, 'condition' => $condition, 'inverse' => $inverse);
    }

    function html_submit($button_text, $class = '', $extra = '', $type = 1)
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

    function draw_submit($button_text, $class = '', $extra = '', $type = 1)
    {
        echo $this->html_submit($button_text, $class, $extra, $type);
    }

    function form_begin()
    {
        $enctype = $add = '';
        if (strlen($this->enctype)) {
            $enctype = ' enctype="' . $this->enctype . '"';
        }
        if (strlen($this->tag_extra)) {
            $add = ' ' . $this->tag_extra;
        }
        return '<form name="' . $this->name . '" action="' . $this->action . '" method="' . $this->method . '"' . $enctype . $add . '>';
    }

    function begin()
    {
        return $this->form_begin();
    }

    function end()
    {
        return
            '<input type="hidden" name="' . $this->name . '_myfrm_sbm" value="1">' .
            '</form>';
    }

    function html($border = 0, $hide_submit = false, $hide_labels = false)
    {
        if (strlen($this->tableWidth)) {
            $ww = ' width="' . $this->tableWidth . '"';
        } else {
            $ww = '';
        }

        $ret = $this->form_begin() .
            '<table cellspacing="' . $this->cellspacing . '" cellpadding="0" border="' . $border . '"' . $ww . '>';

        foreach ($this->fields as $field) {
            $label = '';
            $name = $field->name;
            if (strlen($field->label)) {
                $name = $field->label;
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
            $ret .= $this->html_submit('Submit');
            $ret .= '</td></tr>' . "\n";
        }

        $ret .=
            '</table>' . "\n" . $this->end() . "\n";

        return $ret;
    }

    public function html2()
    {
        $s = $this->form_begin();

        array_walk($this->fields, function ($el) use (&$s) {
            $s .= $el->html();
        });
        $s .= '<input type="button" value="submit" onclick="this.form.submit();">';

        $s .= '</form><br>';

        return $s;
    }

    function draw($border = 0)
    {
        echo $this->html($border);
    }

    function draw_value()
    {
        $this->showValues = true;
        foreach ($this->fields as $field) {
            $this->fields[$field->name]->draw_value = true;
        }
    }

    function draw_html()
    {
        $this->showValues = false;
        foreach ($this->fields as $field) {
            $this->fields[$field->name]->draw_value = false;
        }
    }

    function is_submited()
    {
        return $this->isSubmited();
    }

    function isSubmited()
    {
        return isset($_POST[$this->name . '_myfrm_sbm']);
    }

    function process($force = false)
    {
        if ($this->wasProcess && $force == false) {
            return true;
        }

        if (!isset($_POST[$this->name . '_myfrm_sbm'])) {
            return false;
        }

        $this->errors = array();
        foreach ($this->fields as $key => $field) {
            $this->fields[$key]->process();
            if ($this->fields[$key]->preg_check) {
                if (!preg_match($this->fields[$key]->preg_check, $this->fields[$key]->value)) {
                    $this->errors[] = $field->name;
                }
            }
        }

        $this->check_rules();
        if (sizeof($this->errors)) {
            return false;
        }
        $this->wasProcess = true;
        return true;
    }

    function check_rules()
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
                $str .= preg_match($val, $this->fields[$field]->value) . ' ' . $cond . ' ';
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

    function clear()
    {
        foreach ($this->fields as $field) {
            $this->fields[$field->name]->value = null;
        }
    }

    public function getValue($name)
    {
        return $this->fields[$name]->value;
    }

    public function getValues()
    {
        $r = array();
        foreach ($this->fields as $field) {
            $r[$field->name] = $field->value;
        }
        return $r;
    }
}