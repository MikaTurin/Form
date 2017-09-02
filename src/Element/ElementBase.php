<?php namespace Msz\Forms\Element;

use Msz\Forms\Base;
use Msz\Forms\Exception;
use Msz\Forms\Validation\ValidationBase;

abstract class ElementBase extends Base
{
    const POSITION_INPUT = 1;
    const POSITION_BUTTON = 2;


    protected $label;
    /** @var ValidationBase[] */
    protected $validation = array();
    protected $errors = array();

    protected $useStripTags = true;
    protected $useStripSlashes = true;
    protected $position = self::POSITION_INPUT;

    public function __construct($name, array $attributes = null)
    {
        $this->setAttribute('name', $name);

        if ($attributes) {
            $this->attributes = array_merge($this->attributes, $attributes);
        }
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function handleRequest(array $request = null)
    {
        if (is_null($request)) {
            $request = $_REQUEST;
        }

        if (!isset($request[$this->getName()])) {
            return;
        }

        $s = $request[$this->getName()];

        if ($this->useStripSlashes) {
            $s = stripslashes($s);
        }

        if ($this->useStripTags) {
            $s = preg_replace('/<.*>/isU', '', $s);
        }

        $this->setValue(trim($s));
    }

    public function isValid()
    {
        $valid = true;

        if (!empty($this->validation)) {
            foreach ($this->validation as $validation) {
                if (!$validation->isValid($this->getValue())) {
                    $this->errors[] = str_replace("%element%", $this->getName(), $validation->getMessage());
                    $valid = false;
                }
            }
        }
        return $valid;
    }

    public function html()
    {
        return '<input' . $this->getAttributesHtml() . '/>';
    }

    public function render($view)
    {
        $view->render($this);
    }

    /* ----------------------------------*/
    /* ----- setter & getters below -----*/
    /* ----------------------------------*/

    public function getName()
    {
        return $this->getAttribute('name');
    }

    public function getValue()
    {
        return $this->getAttribute('value');
    }

    public function setValue($value)
    {
        $this->setAttribute('value', $value);

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

    public function setValidation($validation)
    {
        if (!is_array($validation)) {
            $validation = array($validation);
        }

        foreach($validation as $object) {

            if($object instanceof ValidationBase) {
                $this->validation[] = $object;
            }
            else {
                throw new Exception('not ValidationBase');
            }
        }

        return $this;
    }

    public function setUseStripSlashes($useStripSlashes)
    {
        $this->useStripSlashes = $useStripSlashes;

        return $this;
    }

    public function setUseStripTags($useStripTags)
    {
        $this->useStripTags = $useStripTags;

        return $this;
    }

    public function setClass($class)
    {
        $this->setAttribute('class', $class);

        return $this;
    }

    public function addClass($class)
    {
        $this->appendAttribute('class', $class);

        return $this;
    }

    public function setReadOnly()
    {
        $this->setAttribute('readonly', 'readonly');

        return $this;
    }

    public function setStyle($style)
    {
        $this->setAttribute('style', rtrim(trim($style),';') . ';');        
    }
    
    public function addStyle($style)
    {
        $this->appendAttribute('style', rtrim(trim($style),';') . ';');

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        if (!in_array($position, array(self::POSITION_INPUT, self::POSITION_BUTTON))) {
            throw new Exception('incorrect set type: ' . $position);
        }
        $this->position = $position;

        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}