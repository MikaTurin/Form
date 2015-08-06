<?php
class myform_datatable extends myform_control
{
  var $columns = array ();
  var $dataHeight = 150;
  var $value;
  var $rowDataExtra;

  function myform_datatable ($frm_name, $name)
  {
    $this->myform_control ($frm_name, $name);
    $this->value = array ();
  }

  function addColumn ($name, $title, $width, &$obj)
  {
    $this->columns[$name] = new myform_datatable_column ($name, $title, $width, $obj);
  }

  function loadData ($r)
  {
    $this->value = $r;
  }

  function html ()
  {
    $totalww = 0;

    $head = '<table cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;"><tr>';

    foreach ($this->columns as $k => $v)
    {
      $ww = $v->width;
      $totalww += $ww+2;
      $title = $v->title;

      $head .= '<td style="width:'.$ww.';border:1px solid black;border-bottom:none;height:16px;background-color:threedface;"><div class="outset1" style="padding:2px;">'.$title.'</div></td>';
    }

    $head .= '</tr></table>';


    $rows = sizeof ($this->value);
    $cols = sizeof ($this->columns);

    $extra = '';
    if (strlen ($this->rowDataExtra)) $extra = ' '.$this->rowDataExtra;

    $data = '<table cellspacing=0 cellpadding=0 border=0 class="dataTable">';
    for ($i=0; $i<$rows; $i++)
    {
      $data .= '<tr'.$extra.'>';
      reset ($this->columns);
      while (list($k, $v) = each ($this->columns)) $data .= $this->columns[$k]->html ($i, $this->value[$i][$k]);
      $data .= '</tr>';
    }
    $data .= '</table>';

    $totalww += 16;

    $div1 = $div2 = '';
    if ($this->dataHeight)
    {
      $div1 = '<div style="height:'.$this->dataHeight.';overflow:auto;">';
      $div2 = '</div>';
    }
    $s =
    '<table cellspacing="0" cellpadding="0" border="0">'.
    '<tr><td>'.$head.'</td></tr>'.
    '<tr><td>'.$div1.$data.$div2.'</td></tr>'.
    '</table>';


    return $s;
  }

  function process ()
  {
    $r = '';
    foreach ($this->columns as $k => $v)
    {
      if (is_array ($_POST[$k]))
      {
        $strip = $this->columns[$k]->stripslashes;
        $cnt = sizeof ($_POST[$k]);
        for ($i=0; $i<$cnt; $i++)
        {
          if (!isset ($r[$i])) $r[$i] = array ();
          if ($strip) $val = stripslashes ($_POST[$k][$i]); else $val = $_POST[$k][$i];
          $r[$i][$k] = $val;
        }

      }
    }
    $this->value = $r;
  }
}

class myform_datatable_column
{
  var $name;
  var $title;
  var $width;
  var $obj;
  var $div_extra;
  var $td_extra;
  var $stripslashes = true;

  function myform_datatable_column ($name, $title, $width, &$obj)
  {
    $this->name = $name;
    $this->title = $title;
    $this->width = $width;
    $this->obj = $obj;
    $this->td_extra = $this->div_extra = '';
  }

  function html ($idx, $value)
  {
    $this->obj->rename ($this->name.'['.$idx.']');
    $this->obj->value = $value;
    $s = $this->obj->html();

    $s = str_replace ('#ROW', $idx, $s);

    //bgcolor=white
    $this->div_extra .= ' style="width:'.$this->width.'"';
    $this->div_extra = trim ($this->div_extra);
    if (strlen ($this->td_extra)) $td_extra = ' '.$this->td_extra; else $td_extra = '';
    if (strlen ($this->div_extra)) $div_extra = ' '.$this->div_extra; else $div_extra = '';

    return '<td'.$td_extra.'><div'.$div_extra.'>'.$s.'</div></td>';
  }
}
?>
