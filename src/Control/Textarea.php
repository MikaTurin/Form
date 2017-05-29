<?php namespace Msz\Forms\Control;

class Textarea extends Base
{
    var $cols = 40;
    var $rows = 5;

    function html()
    {
        if ($this->drawValue) {
            return $this->htmlValue();
        }

        $extra = $this->generateExtra(array(
            'name' => $this->name,
            'class' => $this->class,
            'cols' => $this->cols,
            'rows' => $this->rows
        ));

        return '<textarea'.$extra.'>'.htmlspecialchars($this->value).'</textarea>';
    }

    public function setCols($cols)
    {
        $this->cols = $cols;
        return $this;
    }

    public function setRows($rows)
    {
        $this->rows = $rows;
        return $this;
    }
}