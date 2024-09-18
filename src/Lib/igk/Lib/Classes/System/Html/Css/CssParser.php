<?php

// @author: C.A.D. BONDJE DOUE
// @filename: CssParser.php
// @date: 20220310 11:16:25
// @desc: parse css 
namespace IGK\System\Html\Css;

use ArrayAccess;
use Exception;
use Error;
use IGK\Css\CssSupport;
use IGK\Helper\StringUtility;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\StringBuilder;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKException;
use ReflectionException;

/**
 * parse css litteral to object definition
 * @package IGK\System\Html\Css
 * 
 */
class CssParser implements ArrayAccess
{
    /**
     * origin 
     * @var mixed
     */
    private $m_source;
    /**
     * get definition 
     * @var mixed
     */
    private $m_definition;
    use ArrayAccessSelfTrait;
    const MATCH_VAR = "/^var\s*\((?P<name>[^\),]+)(\s*,(?P<arg>[^\)]+))?\s*\)$/i";
    const MATCH_RESOLVE_VAR = "/var\s*\((?P<name>[^\),]+)(\s*,(?P<arg>[^\)]+))?\s*\)/i";

    var $lineFeed = '';

    private function __construct()
    {
    }
    /**
     * get json definition
     * @return string|false 
     */
    public function to_json($mode = JSON_PRETTY_PRINT)
    {
        return json_encode($this->m_definition, $mode);
    }
    /**
     * 
     * @return mixed 
     */
    public function to_array()
    {
        return $this->m_definition;
    }
    private static function _join_css_tab($d, $k)
    {
        return $k . ":" . $d;
    }
    public function to_css()
    {
        return implode("\n", array_map(function ($d, $c) {
            if (is_array($d)) {
                $v = $c . "{\n" . implode(";", array_map([self::class, "_join_css_tab"], $d, array_keys($d))) . ";\n}";
            } else {
                $v =  $c . ": " . $d . ";";
            }
            return $v;
        }, $this->m_definition, array_keys($this->m_definition)));
    }
    private static function __ReadDefinition(string $content, &$errors = null)
    {
        $def = [];
        $len = strlen($content);
        $pos = 0;
        $selector = '';
        $name = '';
        $mode = 0; // 0 = global, 1: child selecte 
        $value = '';
        $rv = "";
        $media = null; // store media definition 
        $media_start = 0;
        $errors = [];
        $tdef = [];
        $v_depth = 0; // to skip line comment detection 
        while ($pos < $len) {
            $ch = $content[$pos];
            switch ($ch) {
                case '@':
                    // + | --------------------------------------------------------------------
                    // + | read css attribute definition - directive 
                    // + |

                    $s = $pos;
                    $pos++;
                    $v_name = self::_ReadName($content, $pos, $len);
                    switch ($v_name) {
                        case 'supports':
                            $cdef = self::_ReadSupport($content, $pos, $len, $media, $errors);
                            if ($cdef === false) {
                                return false;
                            }
                            $media = $cdef;
                            $rv = '';
                            $name = '';
                            $def[] = $media;
                            array_push($tdef, $def);
                            $def = &$media->def;
                            $mode = 0; // start reading child definition 
                            break;
                        case 'keyframes':
                            $cdef = self::_ReadKeyFrames($content, $pos, $len, $media, $errors);
                            if ($cdef === false) {
                                return false;
                            }
                            $media = $cdef;
                            $rv = '';
                            $name = '';
                            $mode = 0;
                            $def[] = $media;
                            array_push($tdef, $def);
                            $def = &$media->def;
                            break;
                        // case 'charset': // options\
                            // break;
                        case 'media':
                            // read @media definition 
                            $s = $pos;
                            $pos = strpos($content, '{', $pos);
                            if ($pos === false) {
                                $errors[] = 'media definition not correct';
                                return false;
                            }
                            // read condition in break
                            $g = trim(substr($content, $s, $pos - $s));
                            // reduce ( condition block )
                            $g = StringUtility::ReduceConditionBlock($g);

                            $pos--;
                            $media = new CssMedia($g);
                            $rv = '';
                            $name = '';
                            $mode = 0;
                            $def[] = $media;
                            array_push($tdef, $def);
                            $def = &$media->def;
                            break;
                        case 'color-profile':
                        case 'container':
                        case 'counter-style':
                        case 'font-face':
                        case 'font-feature-values':
                        case 'import':
                        case 'layer':
                        case 'namespace':
                        case 'page':
                        case 'property':
                            $s = $pos;
                            $pos = strpos($content, '{', $pos + 1);
                            if ($pos === false) {
                                $errors[] = 'media definition not correct';
                                return false;
                            }
                            $g = trim(substr($content, $s, $pos - $s));
                            $pos--;
                            $media = new CssProperty($v_name, $g);
                            $rv = '';
                            $name = '';
                            $mode = 0;
                            $def[] = $media;
                            array_push($tdef, $def);
                            $def = &$media->def;
                            break;

                        default:
                            // +| read options or definition states
                            $v_tpos = strpos($content, ';', $pos + 1);
                            $v_npos = strpos($content, '{', $pos + 1);
                            $g = '';
                            $roptions = true;
                            if ((($v_tpos === false) &&  ($v_npos !== false)) ||
                                (($v_npos !== false) && ($v_tpos !== false) && ($v_npos < $v_tpos))
                            ) {
                                // read options
                                $roptions = false;
                            }
                            if ($roptions) {
                                $gv = '';
                                if ($v_tpos !== false) {
                                    $g = trim(substr($content, $s, $pos - $s));
                                    $gv = substr($content, $pos, $v_tpos-$pos);
                                    $pos = $v_tpos;
                                }
                                if ($mode == 0) {
                                    $def[] = new CssOptions($g .".".$gv);
                                }
                            } else {
                                //+ skip reading until branked end.
                                $g = igk_str_read_brank($content, $v_npos, "}", "{");
                                $pos = $v_npos;
                                $mode = 0;
                            }
                            break;
                    }
                    break;
                case '(':
                    $v_depth++;
                    $rv .= $ch;
                    break;
                case '(':
                    $rv .= $ch;
                    $v_depth--;
                    break;
                case '/':
                    // + detect comment 
                    $lpos = $pos + 1;
                    if ($lpos < $len) { 
                        if ($content[$lpos] == '*') {
                            $s = $pos;
                            $pos = strpos($content, '*/', $pos + 1);
                            if ($pos !== false) {
                                $pos += 2;
                            } else {
                                // + | missing close comment  
                                // + | igk_debug_wln('missing close comment');
                                return false;
                            }
                            $g = substr($content, $s, $pos - $s);
                            if ($mode == 0) { // global comment
                                $def[] = new CssComment($g);
                            } else {
                                $rv .= $g;
                            }
                            //+ | fix comment reading.
                            $pos--;
                        } else if (($v_depth == 0) && ($content[$lpos] == '/')) {
                            // skip single line comment
                            $ef = strpos($content, "\n", $pos);
                            if ($ef === false) {
                                $pos = $len;
                            } else {
                                $pos = $ef;
                                $ch = '';
                            }
                        } else {
                            $rv .= $ch; 
                        }
                    }
                    break;
                case '{':
                    if ($media) {
                        if ($media_start == 0) {
                            $media_start++;
                            // + | start reading definition
                            // $mode = 0;
                            break;
                        }
                        $media_start++;
                    }
                    if ($mode == 0) {
                        if (empty($rv = trim($rv))) {
                            igk_trace();
                            igk_die("no selector found.");
                        }
                        $mode = 1;
                        $name = $rv;
                        $selector .= preg_replace('/\s+/', ' ', $name);
                        $rv = '';
                    } else {
                        igk_die(implode("\n", [
                            "start not allowed [mode] = " . $mode,
                            'offset:' . $pos,
                            substr($content, max(0, $pos - 30), 60)
                        ]));
                    }
                    break;
                case ':':
                    // + | in mode = 0 some element can use : as their selector name 
                    if ($mode == 0) {
                        if (empty($rv = trim($rv))) {
                            $name = $ch;
                            $rv = $ch;
                        } else {
                            $rv .= $ch;
                        }
                    } else {
                        if (empty($rv = trim($rv))) {
                            die("name is empty.: " . $rv . ' offset ' . $pos);
                        }
                        $name = $rv;
                        if ($mode == 0) {
                            $selector .= $name . ":";
                        }
                        $rv = '';
                    }
                    break;
                case ';':
                    if (empty($rv = trim($rv)) && (strlen($rv) == 0)) {
                        if ($mode == 0) {
                            igk_die("name is empty." . $rv);
                        }
                    }
                    if ($name) {
                        $value = $rv; 
                        if ($mode == 0) {
                            $def[$name] = $value;
                        } else {
                            $def[$selector][$name] = $value;
                            $name = '';
                            $value = '';
                        }
                        $rv = '';
                    } else {
                        $rv .= $ch;
                    }
                    break;
                case "'":
                case '"': 
                    $rv .= igk_str_read_brank($content, $pos, $ch, $ch);  
                    break;
                case '}':

                    if (!empty($rv = trim($rv))) {
                        // finish selector 
                        if ($mode == 1) {
                            $def[$selector][$name] = $rv;
                            $mode = 0;
                        } else {
                            $def[$name] = $rv;
                        }
                    } else {
                        $mode = 0;
                    }
                    $selector = '';
                    $name = '';
                    $rv = '';
                    if ($media) {
                        $media_start--;
                        if ($media_start == 0) {
                            $mp = array_pop($tdef);
                            $media = null;
                            // change reference
                            $def = &$mp;
                        }
                    }
                    break;
                case "\n":
                case "\r":
                    // 'ignore line break
                    if ($mode != 0) {
                        $rv .= $ch;
                    }
                    break;
                default:
                    $rv .= $ch;
                    break;
            }
            $pos++;
        }
        if (!empty($rv = trim($rv))) {
            if (empty($name)) {
                // extract definition list 
                $converter = new CssStringConverter;
                $converter->NonMarkedStringPropertiesListener = function ($n) {
                    return true; // in_array($n, explode('|', 'content'));
                };
                $def += (array)$converter->read($rv);
            } else {
                $def[$name] = $rv;
            }
            $rv = null;
        }
        return $def;
    }
    private static function _ReadName(string $content, &$pos, $len)
    {
        $p = "";
        while ($pos < $len) {
            $ch = $content[$pos];
            $pos++;
            if (!preg_match('/[0-9a-z_\-\\\]/i', $ch)) {
                break;
            }
            $p .= $ch;
        }
        return $p;
    }
    /**
     * start reading key frames
     * @param string $content 
     * @param int $pos 
     * @param mixed $length 
     * @param mixed $media 
     * @param mixed $error 
     * @return false|CssKeyFrame 
     */
    private static function _ReadKeyFrames(string $content, int &$pos, $length, $media, &$error)
    {
        $s = $pos;
        $pos = strpos($content, '{', $pos + 1);
        if ($pos === false) {
            $errors[] = 'key frame definition not correct';
            $pos = $length;
            return false;
        }
        $v_name = trim(substr($content, $s, $pos - $s));
        $pos--;
        $def = new CssKeyFrame($v_name, $media);
        return $def;
    }
    private static function _ReadSupport(string $content, int &$pos, int $length, $media, &$error)
    {
        $s = $pos;
        $pos = strpos($content, '{', $pos);
        if ($pos === false) {
            $errors[] = 'key frame definition not correct';
            return false;
        }
        $g = trim(substr($content, $s, $pos - $s));
        $pos--;
        $def = new CssSupport($g, $media);
        return $def;
    }
    /**
     * load css style string
     * @param string $content 
     * @return CssParser 
     */
    public static function Parse(string $content): self
    {
        $g = new self();
        $g->m_source = $content;
        $g->m_definition = self::__ReadDefinition($content);
        return $g;
    }

    function _access_OffsetSet($n,  $v)
    {
        $this->m_definition[$n] = $v;
    }
    function _access_OffsetGet($n)
    {
        return igk_getv($this->m_definition, $n);
    }
    function _access_OffsetUnset($n)
    {
        unset($this->m_definition[$n]);
    }
    function _access_offsetExists($n)
    {
        return  isset($this->m_definition[$n]);
    }
    /**
     * retrieve margin definition
     * @return array 
     */
    public function margin()
    {
        return $this->_get_size_def("margin");
    }
    /**
     * retrieve padding definition
     * @return array 
     */
    public function padding()
    {
        return $this->_get_size_def("padding");
    }
    private function _get_size_def($name)
    {
        $t = $r = $b = $l = 'auto';
        if ($m = $this[$name]) {
            $c = array_filter(explode(" ", $m));
            switch (count($c)) {
                case 1:
                    $t = $r = $b = $l = $c[0];
                    break;
                case 2:
                    $t = $b = $c[0];
                    $r = $l = $c[1];
                case 4:
                    break;
                    $t = $c[0];
                    $r = $c[1];
                    $b = $c[2];
                    $l = $c[3];
                default:
                    die("not valid");
                    break;
            }
        }
        if ($g = $this[$name . "-left"]) {
            $l = $g;
        }
        if ($g = $this[$name . "-top"]) {
            $t = $g;
        }
        if ($g = $this[$name . "-right"]) {
            $r = $g;
        }
        if ($g = $this[$name . "-bottom"]) {
            $b = $g;
        }
        return [$t, $r, $b, $l];
    }
    /**
     * parsing position
     * @return array 
     */
    public function position()
    {
        $t = $r = $b = $l = 'auto';
        if (!is_null($g = $this["left"])) {
            $l = $g;
        }
        if (!is_null($g = $this["top"])) {
            $t = $g;
        }
        if (!is_null($g = $this["right"])) {
            $r = $g;
        }
        if (!is_null($g = $this["bottom"])) {
            $b = $g;
        }else if (!is_null($g = $this["height"])) {
            $b = $g;
        }
        return [$t, $r, $b, $l];
    }

    /**
     * retrieve porder definition
     * @return object 
     */
    public function border()
    {

        $res = [];
        if ($all = $this["border"]) {
        }
        if ($all = $this["border-color"]) {
            $res["left"]["color"] =
                $res["right"]["color"] =
                $res["top"]["color"] =
                $res["bottom"]["color"] =
                $all;
        }
        if ($all = $this["border-width"]) {
            $res["left"]["width"] =
                $res["right"]["width"] =
                $res["top"]["width"] =
                $res["bottom"]["width"] =
                $all;
        }

        foreach (["left", "top", "right", "bottom"] as $k) {
            $gp = [];
            if ($w = $this["border-" . $k . "-width"]) {
                ${$k[0] . "w"} = $w;
                $gp["width"] = $w;
            }
            if ($c = $this["border-" . $k . "-color"]) {
                ${$k[0] . "c"} = $c;
                $gp["color"] = $c;
            }
            if ($gp) {
                $res[$k] = (object)$gp;
            }
        }
        unset($k, $gp);

        return (object) $res;
    }

    /**
     * get colors 
     * @return array 
     * @throws Exception 
     * @throws Error 
     * @throws IGKException 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function getColors()
    {
        $colors = [];
        if (!$this->m_definition) {
            return $colors;
        }
        $regx = "/^((background|caret|column-rule|text-decoration|outline|text-emphasis|border(-(left|top|bottom|right))?)-)?color$/";
        $match_var = self::MATCH_VAR;
        $root = igk_getv($this->m_definition, ':root');
        foreach ($this->to_array() as $p => $n) {
            if (!($n instanceof ICssDefinition)) {
                continue;
            }

            // if (is_string($n)) {
            igk_wln("data : ", $n);
            // }
            foreach ($n as $k => $v) {
                if (preg_match($regx, $k)) {
                    // check if support root syntaxe 
                    if ($root &&  preg_match($match_var, $v, $match)) {
                        $cl = self::_ResolvColor($match['name'], $root, $n);
                        $tp = [
                            'type' => 'var',
                            'source' => $v,
                        ];
                        if ($cl) {
                            $v = $cl;
                        } else {
                            $v = $match['name'];
                        }
                        // else {
                        //     $tp = igk_getv($colors, $v);
                        // }
                    } else {
                        $tp = igk_getv($colors, $v);
                    }
                    if ($tp) {
                        if (!is_array($tp)) {
                            $tp = [$tp];
                        } else {
                            $tp[] = $p;
                        }
                        $colors[$v] = $tp;
                    } else
                        $colors[$v] = $p;
                }
            }
        }
        return $colors;
    }
    private static function _ResolvColor(string $name, $root, $section)
    {
        $v = igk_getv($section, $name);
        if ($v) {
            if (preg_match(self::MATCH_VAR, $v, $tab)) {
                // 
                $name = $tab['name'];
            } else {
                return $v;
            }
        }
        return igk_getv($root,  $name);
    }

    /**
     * render document 
     * @return null|string 
     */
    public function render(): ?string
    {
        $sb = new StringBuilder;
        $rdef = $this->m_definition;
        $lf = $this->lineFeed;
        if ($rdef){
            ksort($rdef);
        
            foreach ($rdef as $k => $v) {
                if ($v instanceof ICssDefinition) {
                    $sb->append($v->getDefinition($lf));
                } else {
                    if (is_array($v)) {
                        $sb->append($k . "{".$lf);
                        foreach ($v as $l => $m) {
                            $sb->append(sprintf("%s:%s;", $l, $m).$lf);
                        }
                        $sb->append("}".$lf);
                    } else {
                        if (is_numeric($k)){
                            $sb->append(sprintf("%s;", $v).$lf); 
                        }else{
                            $sb->append(sprintf("%s:%s;", $k, $v).$lf); 
                        } 
                    }
                }
            }
        }
        return $sb;
    }
}
