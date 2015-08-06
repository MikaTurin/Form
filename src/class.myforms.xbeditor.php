<?php

class myform_xbeditor extends myform_control
{
    var $url;
    var $buttons;
    var $tags = array('STRONG', 'B', 'I', 'EM', 'U', 'UL', 'OL', 'LI', 'A', 'BR');
    var $isRichText = false;
    var $isGecko = false;
    var $isMsie = false;
    var $width = '100%';

    function myform_xbeditor($form_name, $name, $url, $buttons, $class = '')
    {
        $this->myform_control($form_name, $name);
        $this->url = $url;
        $this->buttons = $buttons;
        $this->class = $class;
        $this->editor_url = '';
        $this->browser_check();
    }

    function browser_check()
    {
        $s = $_SERVER['HTTP_USER_AGENT'];

        //preg_match ('/safari/isU', $s) ||
        if (preg_match('/konqueror/isU', $s) || preg_match('/opera/isU', $s) || preg_match('/webtv/isU', $s)) {

            $this->isRichText = false;
        } elseif (preg_match('/gecko/isU', $s)) {

            if (preg_match_all('/rv:(\d).(\d)/isU', $s, $r)) {
                if (isset ($r[1][0]) && isset ($r[2][0]) && $r[2][0] >= 3) {
                    $this->isRichText = true;
                }
            } elseif (preg_match('/firefox|chrome|chromium/isU', $s)) {
                $this->isRichText = true;
            } elseif (preg_match_all('/firebird (\d).(\d)/isU', $s, $r)) {
                if (isset ($r[1][0]) && isset ($r[2][0]) && $r[2][0] >= 6) {
                    $this->isRichText = true;
                }
            }
            $this->isGecko = true;
        } elseif (preg_match('/MSIE (\d)./isU', $s, $r)) {
            if (isset ($r[1][0]) && $r[1][0] >= 5) {
                $this->isRichText = true;
            }
            $this->isMsie = true;
        }
    }

    function html()
    {
        global $_CONF;

        $tb = '';
        if ($this->isRichText) {
            $cnt = sizeof($this->buttons);

            for ($i = 0; $i < $cnt; $i++) {
                $style = '';
                if ($i + 1 < $cnt) {
                    $style = ' style="padding-right:5px;"';
                }

                $cmd = $this->buttons[$i]['cmd'];

                if ($cmd == 'spacer') {
                    $tb .= '<td width="' . $this->buttons[$i]['width'] . '"><spacer type="block" width="1" height="1"></td>';
                } else {
                    $js = 'myDesignMode.execCommand(\'' . $cmd . '\', \'\');';
                    if ($cmd == 'quote') {
                        $js = 'alert(myDesignMode.getSource());';
                    }
                    if ($cmd == 'insert_picture') {
                        $js = 'myDesignMode.dialogShow(\'' . $_CONF['public_url'] . 'upload.php\',\'modal\',300,200,\'\');';
                    }

                    $tb .= '<td ' . $style . '><a href="javascript:void(0);" onclick="' . $js . '"><img src="' . $this->url . $this->buttons[$i]['img'] . '" border="0" title="' . $this->buttons[$i]['title'] . '"></td>';
                }
            }

            return
                '<table cellspacing="0" cellpadding="0" border="0" width="' . $this->width . '">' .
                '<tr><td style="padding-bottom:5px"><table cellspacing="0" cellpadding="0" border="0"><tr>' . $tb . '</tr></table></td></tr>' .
                '<tr><td><iframe id="editorWindow" name="editorWindow" class="' . $this->class . '" frameborder="0"></iframe></td></tr>' .
                '</table>' .
                '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value) . '">';
        } else {
            $extra = '';
            if (strlen($this->tag_extra)) {
                $extra = ' ' . $this->tag_extra;
            }
            return '<textarea name="' . $this->name . '" class="' . $this->class . '"' . $extra . '>' . $this->value . '</textarea>';
        }
    }

    function process()
    {
        $this->value = trim(stripslashes($_POST[$this->name]));

        if ($this->isRichText) {
            //echo htmlspecialchars($this->value).'<br><br><br>';

            preg_match_all('/(<p([^>]*)>((\s+)|))+/is', $this->value, $r);
            //dump ($r);

            $this->value = str_replace("\n", ' ', $this->value);
            $this->value = str_replace("\r", '', $this->value);
            $this->value = preg_replace('/^(<p([^>]*)>)*(\s*(&nbsp;)\s*)+(<\/p>)*$/isU', '', $this->value);
            $this->value = preg_replace('/<\!--(.*)-->/isU', '', $this->value);
            $this->value = preg_replace('/<iframe([^>]*)>(.*)<\/iframe>/isU', '', $this->value);
            $this->value = preg_replace('/^(\s*&nbsp;\s*)+/is', '', $this->value);
            $this->value = preg_replace('/<\/p>/isU', '', $this->value);
            $this->value = preg_replace('/<\/table>/isU', '<br><br>', $this->value);


            if ($this->isGecko) {
                if (preg_match_all('/<(span|font|em|strong|div|p)([^>]*)style="([^>]*)"([^>]*)>(.*)<\/(span|font|em|strong|div|p)>/isU',
                    $this->value, $r)) {
                    $cnt = sizeof($r[3]);
                    for ($i = 0; $i < $cnt; $i++) {
                        if (preg_match('/font-style: italic;/isU', $r[3][$i])) {
                            $this->value = str_replace($r[0][$i], '<EM>' . $r[0][$i] . '</EM>', $this->value);
                        }
                        if (preg_match('/font-weight: bold;/isU', $r[3][$i])) {
                            $this->value = str_replace($r[0][$i], '<STRONG>' . $r[0][$i] . '</STRONG>', $this->value);
                        }
                        if (preg_match('/text-decoration: underline;/isU', $r[3][$i])) {
                            $this->value = str_replace($r[0][$i], '<U>' . $r[0][$i] . '</U>', $this->value);
                        }
                    }
                }
            }

            #removing not allowed tags
            preg_match_all('/<((\/|)([a-z]+))(\s[^>]*|)>/isU', $this->value, $r);


            if (is_array($r[1]) && sizeof($r[1])) {
                $r = $r[1];

                $cnt = sizeof($r);
                for ($i = 0; $i < $cnt; $i++) {
                    if (substr($r[$i], 0, 1) == '/') {
                        unset ($r[$i]);
                    } else {
                        $r[$i] = strtoupper($r[$i]);
                    }
                }

                $r = array_unique($r);
                $r = array_values($r);
                $cnt = sizeof($r);

                for ($i = 0; $i < $cnt; $i++) {
                    $tag = $r[$i];
                    if (!in_array($tag, $this->tags) && $tag != 'P') {
                        $this->value = preg_replace('/<(\/|)' . $tag . '(\s[^>]*|)>/isU', '', $this->value);
                    }
                }

            }

            $this->value = preg_replace('/(<br>)*(\s+|)(<p([^>]*)>)/is', '\3', $this->value);
            $this->value = preg_replace('/(<p([^>]*)>)(\s+|)(<br>)*/is', '\1', $this->value);
            $this->value = preg_replace('/(<p([^>]*)>((\s+)|))+/is', '<br><br>', $this->value);

            # removing attributes
            preg_match_all('/<([a-z]+)\s([^>]*)>/isU', $this->value, $r);

            if (is_array($r[1]) && $cnt = sizeof($r[1])) {
                for ($i = 0; $i < $cnt; $i++) {
                    $tag = strtoupper($r[1][$i]);
                    if (!in_array($tag, $this->tags)) {
                        $this->value = preg_replace('/<' . $tag . '\s(.*)>/isU', '', $this->value);
                    } else {
                        if (strtoupper($tag) == 'A') {
                            $url = preg_replace('/(.*)href="([^"]+)"(.*)/is', '\2', $r[2][$i]);
                            $this->correct_url($url);
                            $this->value = str_replace($r[0][$i], '<' . $tag . ' href="' . $url . '" target="_blank">',
                                $this->value);
                        } else {
                            $this->value = preg_replace('/<' . $tag . '\s(.*)>/isU', '<' . $r[$i] . '>', $this->value);
                        }
                    }
                }
            }

            //echo htmlspecialchars($this->value).'<br><br><br>';

            $this->value = preg_replace('/<a([^>]*)>\s*<\/a>/isU', '', $this->value);
            $this->value = preg_replace('/^(\s*<br>\s*)+/is', '', $this->value);
            $this->value = preg_replace('/(\s*<br>\s*)+$/is', '', $this->value);
            $this->value = trim($this->value);

            //echo htmlspecialchars($this->value).'<br><br><br>';
            //echo $this->value.'<br>';
            //exit ();
        } else {
            $this->value = strip_tags($this->value);
            $this->value = nl2br($this->value);
        }
    }

    function correct_url(&$url)
    {
        $r = array('http://', 'https://', 'ftp://', 'gopher://', 'mailto:');
        $tmp = strtolower($url);
        $is = 0;
        for ($i = 0; $i < 5; $i++) {
            $len = strlen($r[$i]);
            if (substr($tmp, 0, $len) == $r[$i]) {
                $is = 1;
                break;
            }
        }
        if (!$is) {
            $url = 'http://' . $url;
        }
    }
}

class myform_richedit extends myform_control
{
    var $url;
    var $buttons;
    var $tags = array(
        'STRONG',
        'B',
        'I',
        'EM',
        'U',
        'UL',
        'OL',
        'LI',
        'A',
        'BR',
        'IMG',
        'STRIKE',
        'P',
        'H1',
        'H2',
        'H3'
    );


    function __construct($form_name, $name, $class = '')
    {
        $this->myform_control($form_name, $name);
        $this->class = $class;
        $this->strip_tags = false;
    }

    function html()
    {
        if ($this->draw_value) {
            return nl2br(htmlspecialchars($this->value));
        }

        $class = '';
        $extra = $this->generate_extra();

        if (strlen($this->class)) {
            $class = ' class="' . $this->class . '"';
        }
        return '<textarea name="' . $this->name . '"' . $class . $extra . '>' . $this->value . '</textarea>';
    }

    function process()
    {
        $this->value = trim(stripslashes($_POST[$this->name]));
        $start = $this->value;

        if (1) {
            //echo htmlspecialchars($this->value).'<br><br><br>';

            //preg_match_all ('/(<p([^>]*)>((\s+)|))+/is', $this->value, $r);
            //dump ($r);

            $this->value = str_replace("\n", ' ', $this->value);
            $this->value = str_replace("\r", '', $this->value);
            $this->value = preg_replace('/^(<p([^>]*)>)*(\s*(&nbsp;)\s*)+(<\/p>)*$/isU', '', $this->value);
            $this->value = preg_replace('/<\!--(.*)-->/isU', '', $this->value);

            $this->value = preg_replace('/<iframe([^>]*)>(.*)<\/iframe>/isU', '', $this->value);
            $this->value = preg_replace('/^(\s*&nbsp;\s*)+/is', '', $this->value);
            //$this->value = preg_replace ('/<\/p>/isU', '', $this->value);
            $this->value = preg_replace('/<\/table>/isU', '<br><br>', $this->value);


            if (1) {
                if (preg_match_all('/<(span|font|em|strong|div)([^>]*)style="([^>]*)"([^>]*)>(.*)<\/(span|font|em|strong|div)>/isU',
                    $this->value, $r)) {
                    $cnt = sizeof($r[3]);
                    for ($i = 0; $i < $cnt; $i++) {
                        if (preg_match('/font-style: italic;/isU', $r[3][$i])) {
                            $this->value = str_replace($r[0][$i], '<EM>' . $r[0][$i] . '</EM>', $this->value);
                        }
                        if (preg_match('/font-weight: bold;/isU', $r[3][$i])) {
                            $this->value = str_replace($r[0][$i], '<STRONG>' . $r[0][$i] . '</STRONG>', $this->value);
                        }
                    }
                }
            }

            #removing not allowed tags
            preg_match_all('/<((\/|)([a-z0-9]+))(\s[^>]*|)>/isU', $this->value, $r);

            if (is_array($r[1]) && sizeof($r[1])) {
                $r = $r[1];

                $cnt = sizeof($r);
                for ($i = 0; $i < $cnt; $i++) {
                    if (substr($r[$i], 0, 1) == '/') {
                        unset($r[$i]);
                    } else {
                        $r[$i] = strtoupper($r[$i]);
                    }
                }

                $r = array_unique($r);
                $r = array_values($r);
                $cnt = sizeof($r);

                for ($i = 0; $i < $cnt; $i++) {
                    $tag = $r[$i];
                    if (!in_array($tag, $this->tags)) {
                        $this->value = preg_replace('/<(\/|)' . $tag . '(\s[^>]*|)>/isU', '', $this->value);
                    }
                }
            }

            $this->value = preg_replace('/(<br>)*(\s+|)(<p([^>]*)>)/is', '\3', $this->value);
            $this->value = preg_replace('/(<p([^>]*)>)(\s+|)(<br>)*/is', '\1', $this->value);
            //$this->value = preg_replace ('/(<p([^>]*)>((\s+)|))+/is', '<br><br>', $this->value);

            # removing attributes
            preg_match_all('/<([a-z]+)\s([^>]*)>/isU', $this->value, $r);

            if (is_array($r[1]) && $cnt = sizeof($r[1])) {
                for ($i = 0; $i < $cnt; $i++) {
                    $tag = strtoupper($r[1][$i]);
                    if (!in_array($tag, $this->tags)) {
                        $this->value = preg_replace('/<' . $tag . '\s(.*)>/isU', '', $this->value);
                    } else {
                        if (strtoupper($tag) == 'A') {
                            $url = preg_replace('/(.*)href="([^"]+)"(.*)/is', '\2', $r[2][$i]);
                            $this->correct_url($url);
                            $this->value = str_replace($r[0][$i], '<' . $tag . ' href="' . $url . '" target="_blank">',
                                $this->value);
                        } elseif (strtoupper($tag) == 'IMG') {
                            $r[2][$i] = str_replace("'", '"', $r[2][$i]);
                            $src = trim(preg_replace('/(.*)src="([^"]+)"(.*)/is', '\2', $r[2][$i]));

                            $img = '<' . $tag . ' src="' . $src . '"' . $this->getAlignAttr($r[2][$i]) . '>';

                            if (strtoupper(substr($src, 0, 4)) != 'HTTP') {
                                $img = '';
                            }
                            $this->value = str_replace($r[0][$i], $img, $this->value);
                        } else {
                            $this->value = preg_replace('/<' . $tag . '\s(.*)>/isU', '<' . $r[$i] . '>', $this->value);
                        }
                    }
                }
            }

            //echo htmlspecialchars($this->value).'<br><br><br>';

            $this->value = preg_replace('/<a([^>]*)>\s*<\/a>/isU', '', $this->value);
            $this->value = preg_replace('/^(\s*<br>\s*)+/is', '', $this->value);
            $this->value = preg_replace('/(\s*<br>\s*)+$/is', '', $this->value);
            $this->value = trim($this->value);
        } else {
            $this->value = strip_tags($this->value);
            $this->value = nl2br($this->value);
        }

        //$this->value .= '<Array>';

        if (preg_match('/<Array>/isU', $this->value)) {

            mail('mika.turin@gmail.com', 'wallstreet: incorrect html parse', $start);
        }
    }

    protected function getAlignAttr($s)
    {
        $alignAttr = '';
        $alignAllowed = array('left', 'right');
        $align1 = strtolower(trim(preg_replace('/(.*)align="([^"]+)"(.*)/is', '\2', $s)));
        $align2 = preg_replace('/(.*)float:\s*([a-z]{4,5})\s*;?(.*)/is', '\2', $s);


        if (in_array($align1, $alignAllowed)) {
            $alignAttr = $align1;
        }
        if (in_array($align2, $alignAllowed)) {
            $alignAttr = $align2;
        }

        if ($alignAttr == 'left') {
            $alignAttr = ' style="float:left; margin:0px 10px 10px 0px"';
        }
        if ($alignAttr == 'right') {
            $alignAttr = ' style="float:right; margin:0px 0 10px 10px"';
        }

        return $alignAttr;
    }

    function correct_url(&$url)
    {
        $r = array('http://', 'https://', 'ftp://', 'gopher://', 'mailto:');
        $tmp = strtolower($url);
        $is = 0;
        for ($i = 0; $i < 5; $i++) {
            $len = strlen($r[$i]);
            if (substr($tmp, 0, $len) == $r[$i]) {
                $is = 1;
                break;
            }
        }
        if (!$is) {
            $url = 'http://' . $url;
        }
    }
}

