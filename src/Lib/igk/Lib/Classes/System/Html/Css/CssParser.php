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
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\StringBuilder;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;
use ReflectionException;
// + | --------------------------------------------------------------------
// + | value delimiter ':' 
// + | field delimiter ';'
// + |

/**
 * parse css litteral to object definition 
 * @package IGK\System\Html\Css
 * @note delimiter : field delimiter ;
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
    /**
    * Constant: match var.
    * @var mixed
    */
    const MATCH_VAR = "/^var\s*\((?P<name>[^\),]+)(\s*,(?P<arg>[^\)]+))?\s*\)$/i";
    /**
    * Constant: match resolve var.
    * @var mixed
    */
    const MATCH_RESOLVE_VAR = "/var\s*\((?P<name>[^\),]+)(\s*,(?P<arg>[^\)]+))?\s*\)/i";
    /**
    * Property: line feed.
    * @var mixed
    */
    var $lineFeed = '';
    /**
    * .ctr
    * @return mixed
    */
    private function __construct() {}
    /**
    * get json definition
    * @param mixed $mode
    * @return string|false
    */
    public function to_json($mode = JSON_PRETTY_PRINT)
    {
        return json_encode($this->m_definition, $mode);
    }
    /**
    * auto generate doc.
    * @return mixed
    */
    public function to_array()
    {
        return $this->m_definition;
    }
    /**
    * auto generate doc.
    * @param mixed $d
    * @param mixed $k
    * @return mixed
    */
    private static function _join_css_tab($d, $k)
    {
        return $k . ":" . $d;
    }
    /**
    * To css.
    */
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
    /**
    * read definition
    * @param string $content
    * @param null|mixed & $errors
    * @return mixed|false
    */
    private static function _ReadDefinition(string $content, &$errors = null)
    {
        $def = [];
        $len = strlen($content);
        $pos = 0;
        $selector = '';
        $name = '';
        $mode = 0; 
        $value = '';
        $rv = "";
        $media = null; 
        $media_start = 0;
        $errors = [];
        $tdef = [];
        $v_depth = 0;
        $v_skip_space = false;

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
                            $mode = 0; 
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
                        case 'media':
                            $s = $pos;
                            $pos = strpos($content, '{', $pos);
                            if ($pos === false) {
                                $errors[] = 'media definition not correct';
                                return false;
                            }
                            $g = trim(substr($content, $s, $pos - $s));
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
                            
                            if (method_exists(static::class, $fc = '_Read'.ucfirst($v_name).'Rule')){
                                call_user_func_array([static::class, $fc], [[
                                    'pos'=>& $pos,
                                    'content'=>$content,
                                    'def'=>& $def,
                                    'rule'=>$v_name
                                ]]);
                            }
                            else {
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
                            }
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
                                $roptions = false;
                            }
                            if ($roptions) {
                                $gv = '';
                                if ($v_tpos !== false) {
                                    $g = trim(substr($content, $s, $pos - $s));
                                    $gv = substr($content, $pos, $v_tpos - $pos);
                                    $pos = $v_tpos;
                                }
                                if ($mode == 0) {
                                    $def[] = new CssOptions($g . "." . $gv);
                                }
                            } else {
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
                case ')':
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
                            if ($mode == 0) { 
                                $def[] = new CssComment($g);
                            } else {
                                $rv .= $g;
                            }
                            $pos--;
                        } else if (($v_depth == 0) && ($content[$lpos] == '/')) {
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
                            break;
                        }
                        $media_start++;
                    }
                    if ($mode == 0) {
                        if (empty($rv = trim($rv))) {
                            igk_trace();
                            igk_wln_e($content);
                            igk_die("no selector found.");
                        }
                        $mode = 1;
                        $name = $rv;
                        $selector .= $name; // preg_replace('/\s+/', ' ', $name);
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
                    // + | in mode = 0 some element can use : as their selector name  - change the multi selector properties                   
                    if ($mode == 0) {
                        // if (empty($rv = trim($rv))) {
                        //     $name = $ch;
                        //     $rv = $ch;
                        // } else {
                            $rv .= $ch;
                        // }
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
                            igk_die("[cssparser] - name is empty after ';' invalid syntax" . json_encode(compact("rv", "content")));
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
                            $def = &$mp;
                        }
                    }
                    break;
                case "\n":
                case "\r":
                    if ($mode != 0) {
                        $rv .= $ch;
                    }
                    break;
                default:
                    if (($ch == ' ') && ($mode==0)){
                        if ($v_skip_space){
                            $ch = '';
                        } 
                        $v_skip_space = true;
                    } else {
                        $v_skip_space = false;
                    }
                    $rv .= $ch;
                    break;
            }
            $pos++;
        }
        if (!empty($rv = trim($rv))) {
            if (empty($name)) {
                $converter = new CssStringConverter;
                $converter->NonMarkedStringPropertiesListener = function ($n) {
                    return true; 
                };
                $def += (array)$converter->read($rv);
            } else {
                $def[$name] = $rv;
            }
            $rv = null;
        }
        return $def;
    }
    /**
    * auto generate doc.
    * @param string $content
    * @param mixed & $pos
    * @param mixed $len
    * @return mixed
    */
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
     * read class name definition 
     * @param string $content 
     * @return string|null 
     */
    public static function ReadClassName(string $content){

        // return CssClassNameUtility::ReadClassName($content);

        if (preg_match('/((?:\\.)?[a-z][0-9a-z_\-]*(?:\\\\.[a-z][0-9a-z_\-]*)*)\\b/i', $content, $tab)) {
            return $tab[1];
        }
        $s = preg_last_error_msg();
        igk_dev_wln('regex-error? ', $s);
        return null;
    }
    /**
    * start reading key frames
    * @param string $content
    * @param int & $pos
    * @param int $pos
    * @param int $length
    * @param mixed $media
    * @param mixed & $error
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
    /**
    * auto generate doc.
    * @param string $content
    * @param int & $pos
    * @param int $length
    * @param mixed $media
    * @param mixed & $error
    * @return mixed
    */
    private static function _ReadSupport(string $content, int &$pos, int $length, $media, &$error)
    {
        $pos--;
        $g = self::_ReadCondition($content, $pos, $regex);

        // $s = $pos;
        // $pos = strpos($content, '{', $pos);
        // if ($pos === false) {
        //     $errors[] = 'key frame definition not correct';
        //     return false;
        // }
        // $g = trim(substr($content, $s, $pos - $s));
        $pos--;
        $def = new CssSupport($g, $media);
        return $def;
    }
    private static function _ReadCondition(string $src, int & $pos, & $regex = null ){
        if (is_null($regex)){
            $regex = new RegexMatcherContainer;           
            $str = $regex->appendStringDetection()->last();
            $comment = $regex->appendMultilineComment()->last();
            $cond = $regex->begin('\(', '\)', 'condition')->last();
            $space = $regex->match('\\s+','space')->last();            
            $cond->patterns = [
                $space,
                $str,
                $comment,
                $cond
                ];
            
        }
        $regex->resetTreatment();
        $sb = '';
        $offset = $pos;
        while($g = $regex->detect($src, $pos)){
            if ($e = $regex->end($g, $src, $pos)){
                if (is_null($e->parentInfo)){
                    if ($e->tokenID == 'condition'){
                        $sb.= substr($src, $offset, $e->to-$offset);
                        return $sb;
                    }
                } else{
                    if ($e->tokenID=='space'){
                        $sb.= substr($src, $offset, $e->from-$offset).' ';
                        $offset = $e->to;
                    }
                }
            }
        }
        return null;
    }
    /**
     * load css style string
     * @param string $content 
     * @return CssParser 
     */
    public static function Parse(string $content)
    {
        $r = self::_ReadDefinition($content);
        if (false !== $r){
            $g = new self();
            $g->m_source = $content;
            $g->m_definition = $r;
            return $g;
        }
    }
    /**
    * Access offset set.
    * @param mixed $n
    * @param mixed $v
    */
    function _access_OffsetSet($n,  $v)
    {
        $this->m_definition[$n] = $v;
    }
    /**
    * Access offset get.
    * @param mixed $n
    */
    function _access_OffsetGet($n)
    {
        return igk_getv($this->m_definition, $n);
    }
    /**
    * Access offset unset.
    * @param mixed $n
    */
    function _access_OffsetUnset($n)
    {
        unset($this->m_definition[$n]);
    }
    /**
    * Access offset exists.
    * @param mixed $n
    */
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
    /**
    * auto generate doc.
    * @param mixed $name
    * @return mixed
    */
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
        } else if (!is_null($g = $this["height"])) {
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
            if ($w = $this["border-" . $k . "-width"]) {
                $res[$k]['width'] = $w;
            }
            if ($c = $this["border-" . $k . "-color"]) {
                $res[$k]['color'] = $c;
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
            igk_dev_wln(__FILE__.":".__LINE__ , "data : ", $n);
            foreach ($n as $k => $v) {
                if (preg_match($regx, $k)) {
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
    /**
    * auto generate doc.
    * @param string $name
    * @param mixed $root
    * @param mixed $section
    * @return mixed
    */
    private static function _ResolvColor(string $name, $root, $section)
    {
        $v = igk_getv($section, $name);
        if ($v) {
            if (preg_match(self::MATCH_VAR, $v, $tab)) {
                $name = $tab['name'];
            } else {
                return $v;
            }
        }
        return igk_getv($root,  $name);
    }
    /**
    * Treat extrat property.
    * @param mixed & $rdef
    */
    protected function treatExtratProperty(&$rdef)
    {
        if ($size = igk_getv($rdef, 'size')) {
            $tab = explode(' ', $size, 2);
            $c = count($tab);
            if ($c < 2) {
                $tab[1] = $tab[0];
            }
            $rdef['width'] = $tab[0];
            $rdef['height'] = $tab[1];
            unset($rdef['size']);
        }
        if ($size = igk_getv($rdef, 'location')) {
            $tab = explode(' ', $size, 2);
            $c = count($tab);
            if ($c < 2) {
                $tab[1] = $tab[0];
            }
            $rdef['left'] = $tab[0];
            $rdef['top'] = $tab[1];
            unset($rdef['location']);
        }
         if ($s = igk_getv($rdef, 'x')) {           
            $rdef['left'] = $s; 
            unset($rdef['x']);
        }
         if ($s = igk_getv($rdef, 'y')) {           
            $rdef['top'] = $s; 
            unset($rdef['y']);
        }
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
        if ($rdef) {
            $this->treatExtratProperty($rdef);
            ksort($rdef);
            foreach ($rdef as $k => $v) {
                if ($v instanceof ICssDefinition) {
                    $sb->append($v->getDefinition($lf));
                } else {
                    if (is_array($v)) {
                        $sb->append($k . "{" . $lf);
                        foreach ($v as $l => $m) {
                            $sb->append(sprintf("%s:%s;", $l, $m) . $lf);
                        }
                        $sb->append("}" . $lf);
                    } else {
                        if (is_numeric($k)) {
                            $sb->append(sprintf("%s;", $v) . $lf);
                        } else {
                            $sb->append(sprintf("%s:%s;", $k, $v) . $lf);
                        }
                    }
                }
            }
        }
        return $sb;
    }

    /**
     * 
     * @param mixed $options 
     * @return void 
     */
    protected static function _ReadImportRule($options){
        /**
         * @var int $pos
         */
        extract($options, EXTR_REFS);
        $regex = new RegexMatcherContainer;
        
        $regex->match(';', 'end-instruct');
        $regex->appendStringDetection();
        $regex->appendMultilineComment();
        // define
        
        $src = $content;
        $value = '';
        while($g = $regex->detect($src, $pos)){
            if ($e = $regex->end($g, $src, $pos)){
                $id = $e->tokenID;
                if ($id=='end-instruct'){
                    $pos = $e->from;
                    break;
                }
                if ($id=='string'){
                    $value = $e->value;
                }
            }
        }
        if (!empty($value)){
            $g = new CssImportRule;
            $g->value = igk_str_remove_quote($value);
            $def[] = $g;
        }
    }
}