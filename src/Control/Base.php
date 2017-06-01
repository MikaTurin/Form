<?php namespace Msz\Forms\Control;

abstract class Base
{
    const INPUT = 1;
    const BUTTON = 2;

    protected $name;
    protected $class;
    protected $type = self::INPUT;
    protected $value;
    protected $tagExtra = '';
    protected $styles = array();
    protected $drawValue = false;
    protected $saveEmpty = true;
    protected $preg = '';
    protected $label;
    protected $useStripTags = true;
    protected $useStripSlashes = true;
    /** @var callable */
    protected $verifier;

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

    public function addStyle($s) {
        $this->styles[] = $s;

        return $this;
    }

    public function setReadOnly()
    {
        $this->tagExtra .= ' readonly="readonly" ';

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        if (!in_array($type, array(self::BUTTON, self::INPUT))) {
            throw new \Exception('incorrect set type: ' . $type);
        }
        $this->type = $type;

        return $this;
    }

    abstract public function html();

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

        if ($this->useStripSlashes) {
            $s = stripslashes($s);
        }

        if ($this->useStripTags) {
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

        if (sizeof($this->styles)) {
            $s .= ' style="';
            foreach ($this->styles as $style) {
                $s .= rtrim(trim($style), ';') . ';';
            }
            $s .= '"';
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

    public function getVerifier()
    {
        return $this->verifier;
    }

    public function setVerifier(callable $verifier)
    {
        $this->verifier = $verifier;

        return $this;
    }

    public function isUseStripSlashes()
    {
        return $this->useStripSlashes;
    }

    public function setUseStripSlashes($useStripSlashes)
    {
        $this->useStripSlashes = $useStripSlashes;

        return $this;
    }

    public function isUseStripTags()
    {
        return $this->useStripTags;
    }

    public function setUseStripTags($useStripTags)
    {
        $this->useStripTags = $useStripTags;

        return $this;
    }
}