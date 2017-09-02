<?php namespace Msz\Forms;

use Msz\Forms\Element\ElementBase;

class Form extends Base
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /** @var ElementBase[] */
    protected $fields = array();
    protected $errors = array();
    protected $rules = array();
    protected $tableWidth = '';
    protected $wasProcess = false;

    function __construct($name, $action = null, $method = null)
    {
        $this->fields = array();
        $this->setName($name);
        $this->setAction($action);
        if (!is_null($method)) {
            $this->setMethod($method);
        }
        else {
            $this->setMethodPost();
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

    public function addElement(ElementBase $obj)
    {
        $this->fields[$obj->getName()] = $obj;

        return $this;
    }

    public function getName()
    {
        return $this->getAttribute('name');
    }

    public function setName($name)
    {
        $this->setAttribute('name', $name);
    }

    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    public function setMethod($method)
    {
        $method = strtoupper($method);
        if (!in_array($method, array(self::METHOD_GET, self::METHOD_POST))) {
            throw new Exception('incorrect method: ' . $method);
        }
        $this->setAttribute('method', $method);

        return $this;
    }

    public function setMethodGet()
    {
        $this->setMethod(self::METHOD_GET);
    }

    public function setMethodPost()
    {
        $this->setMethod(self::METHOD_POST);
    }

    public function isMethodGet()
    {
        return $this->getMethod() == self::METHOD_GET;
    }

    public function isMethodPost()
    {
        return $this->getMethod() == self::METHOD_POST;
    }

    public function getAction()
    {
        return $this->getAttribute('action');
    }

    public function setAction($action)
    {
        $this->setAttribute('action', $action);

        return $this;
    }

    public function getField($name)
    {
        if (!array_key_exists($name, $this->fields)) {
            throw new Exception('no such field: ' . $name);
        }

        return $this->fields[$name];
    }

    public function setRule($key, $arr, $condition = 'AND', $inverse = 0)
    {
        $this->rules[$key] = array('rule' => $arr, 'condition' => $condition, 'inverse' => $inverse);
        return $this;
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

        return '<form' . $this->getAttributesHtml() . '>';
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

        $buttons = array();

        foreach ($this->fields as $field) {
            if ($field->getPosition() == ElementBase::POSITION_BUTTON) {
                $buttons[] = $field;
                continue;
            }
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


        if (!$hide_submit || sizeof($buttons)) {
            $ret .= '<tr><td colspan="2" align="right"><br>';

            foreach ($buttons as $button) {
                $ret .= $button->html();
            }
            if (!$hide_submit) $ret .= $this->htmlSubmit('Submit');
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
            /** @var ElementBase $el */
            $s .= $el->html();
        });
        $s .= '<input type="button" value="submit" onclick="this.form.submit();">';

        $s .= '</form><br>';

        return $s;
    }

    public function draw($border = 0)
    {
        echo $this->html($border);
        return $this;
    }

    public function isSubmited()
    {
        if ($this->isMethodGet()) {
            return isset($_GET[$this->getName() . '_myfrm_sbm']);
        } elseif ($this->isMethodPost()) {
            return (boolean)sizeof($_POST);
        }

        return false;
    }

    public function process($force = false)
    {
        if ($this->wasProcess && $force == false) {
            return true;
        }

        if (!$this->isSubmited()) {
            return false;
        }

        $this->errors = array();
        foreach ($this->fields as $key => $field) {

            $this->fields[$key]->handleRequest($_REQUEST);

            if (!$this->fields[$key]->isValid()) {
                $this->errors[$field->getName()] = $field->getErrors();
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
            /** @var boolean $res */
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
        return $this->getField($name)->getValue();
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