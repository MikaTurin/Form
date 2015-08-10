<?php namespace Msz\Forms\Control;

class Base
{
    protected $name;
    protected $class;
    protected $type;
    protected $value;
    protected $tagExtra = '';
    protected $drawValue = false;
    protected $saveEmpty = true;
    protected $preg = '';
    protected $label;
    protected $stripTags = true;

    public function __construct($name, $class = null, $value = null)
    {
        $this->name = $name;

        if (!is_null($class)) {
            $this->class = $class;
        }

        if (!is_null($value)) {
            $this->value = $value;
        }
    }

    public static function make($name, $class = null, $value = null)
    {
        return new static($name, $class, $value);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getPreg()
    {
        return $this->preg;
    }

    public function setPreg($preg)
    {
        $this->preg = $preg;
		
		return $this;
    }

    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    public function setTagExtra($tagextra)
    {
        $this->tagExtra = $tagextra;

        return $this;
    }

    public function setReadOnly()
    {
        $this->tagExtra .= ' readonly="readonly" ';

        return $this;
    }

    public function html()
    {
        return '';
    }

    public function htmlValue()
    {
        return htmlspecialchars(trim($this->value));
    }

    public function draw()
    {
        echo $this->html();
    }

    public function process()
    {
        if (!isset($_REQUEST[$this->name])) return;

        $s = $_REQUEST[$this->name];
        $s = stripslashes($s);
        if ($this->stripTags) {
            $s = preg_replace('/<.*>/isU', '', $s);
        }
        $this->value = trim($s);
    }

    protected function generateExtra(array $options = array())
    {
        $s = '';

        foreach ($options as $k => $v) {
            if (empty($v)) continue;
            $s .= ' ' . $k . '="' . $v .'"';
        }

        if (strlen($this->tagExtra)) {
            $s .= ' ' . $this->tagExtra;
        }

        return $s;
    }

    public function htmlHidden()
    {
        return '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '">';
    }

    public function destroy()
    {

    }

}