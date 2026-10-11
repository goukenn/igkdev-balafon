<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssThemeValueDetector.php
// @date: 20241030 06:45:06
namespace IGK\System\Html\Css;
use Exception;
use IGK\Helper\StringUtility;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;

/**
* auto generate doc.
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssThemeValueDetector{ 
    /**
     * treat global
     * @var bool
     */
    var $treatGlobal;
    /**
     * remove static properties
     * @var bool
     */
    var $removeStaticProps;
    /**
    * .ctr
    */
    public function __construct(){
        $this->treatGlobal = true;
        $this->removeStaticProps = true;
    }
    /**
    * Returns true if Global Expression.
    * @param string $name
    */
    public static function IsGlobalExpression(string $name){
        return preg_match("/^\b(resolv|trans|transform|anim(ation)?|sysfcl|syscl|sysbcl)\b\s*:/", $name);
    }
    /**
     * treat litteral expression by removing extra white space
     * @param string $value 
     * @return string|string[]|null 
     */
    private static function _TreatExpression(string $value){
        $v = preg_replace("/\s+/", " ", $value);
        return $v;
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return mixed
    */
    private static function _TreatPropertyExpression(string $value){
        $v = preg_replace("/\s+/", "", $value);
        return $v;
    }
    /**
    * - remove global transform litteral- preserve string expression
    * @param string $v
    * @param bool $remove_global remove global expression
    * @param bool $remove_static_property
    * @throws Exception
    * @return string
    */
    public static function RemoveTransformLitteralFrom(string $v, 
        bool $remove_global=false,
        bool $remove_static_property=false)
    {
        // pure function of its arguments : memoize
        $key = ($remove_global ? '1' : '0') . ($remove_static_property ? '1' : '0') . $v;
        if (isset(self::$sm_removeCache[$key])) {
            return self::$sm_removeCache[$key];
        }
        if (count(self::$sm_removeCache) > 20000) {
            self::$sm_removeCache = [];
        }
        $r = ($remove_global && $remove_static_property) ? self::FastRemoveTransformLitteral($v) : null;
        return self::$sm_removeCache[$key] = $r ?? self::_RemoveTransformLitteralFrom($v, $remove_global, $remove_static_property);
    }
    /**
     * native port of _RemoveTransformLitteralFrom (remove_global and remove_static_property enabled)
     * @return ?string null if the value is not eligible (quotes, unbalanced groups)
     */
    public static function FastRemoveTransformLitteral(string $v): ?string
    {
        if (strpbrk($v, '"\'') !== false) {
            return null;
        }
        $len = strlen($v);
        $n = '';
        $lpos = 0;
        $i = 0;
        $re = '/\{|\(|\s+|\[|(?:-+)?[\w\-]+\s*:\s*/';
        while ($i < $len) {
            if (!preg_match($re, $v, $m, PREG_OFFSET_CAPTURE, $i)) {
                break;
            }
            $tok = $m[0][0];
            $from = $m[0][1];
            $c = $tok[0];
            if ($c == '{') {
                $j = strpos($v, '}', $from + 1);
                if ($j === false) {
                    return null;
                }
                $pos = $j + 1;
                if (preg_match('/\G\s*;\s*/', $v, $m2, 0, $pos)) {
                    $pos += strlen($m2[0]);
                }
                $n = trim($n . substr($v, $lpos, $from - $lpos));
                $lpos = $pos;
            } else if ($c == '(') {
                $j = strpos($v, ')', $from + 1);
                if ($j === false) {
                    return null;
                }
                $pos = $j + 1;
            } else if (ctype_space($c)) {
                $pos = $from + strlen($tok);
                $n = trim($n . substr($v, $lpos, $from - $lpos)) . ' ';
                $lpos = $pos;
            } else if ($c == '[') {
                $j = strpos($v, ']', $from + 1);
                if ($j === false) {
                    return null;
                }
                $pos = $j + 1;
                if (preg_match('/\G\s*;\s*/', $v, $m2, 0, $pos)) {
                    $pos += strlen($m2[0]);
                }
                $tv = preg_replace("/\\s+/", " ", substr($v, $from, $pos - $from));
                if (self::IsGlobalExpression(trim($tv, StringUtility::DEFAULT_TRIM_CHAR . '[];'))) {
                    $n .= substr($v, $lpos, $from - $lpos);
                } else {
                    $n .= substr($v, $lpos, $from - $lpos) . $tv;
                }
                $lpos = $pos;
            } else { // property
                $pos = $from + strlen($tok);
                $bvalue = substr($v, $pos);
                if (!preg_match('/;|\[/', $bvalue, $m3, PREG_OFFSET_CAPTURE)) {
                    $n .= substr($v, $lpos, $from - $lpos);
                    $lpos = $len + 1;
                    break;
                }
                if ($m3[0][0] == ';') {
                    $n .= substr($v, $lpos, $from - $lpos);
                    $lpos = $pos + $m3[0][1] + 1;
                    $pos = $lpos;
                } else {
                    $k = strpos($bvalue, ']', $m3[0][1] + 1);
                    if ($k === false) {
                        return null;
                    }
                    $ln = $k + 1;
                    $x = self::_TreatPropertyExpression($tok) . substr($bvalue, 0, $ln);
                    $x = self::_TreatExpression($x);
                    $n .= substr($v, $lpos, $from - $lpos) . $x;
                    $pos += $ln;
                    $lpos = $pos;
                }
            }
            $i = $pos;
        }
        if ($lpos <= $len) {
            $n .= substr($v, $lpos);
        }
        return trim($n);
    }
    private static $sm_removeCache = [];
    private static function _RemoveTransformLitteralFrom(string $v, bool $remove_global, bool $remove_static_property)
    { 
        // fast path : only plain "name: value;" declarations -> every static property is removed
        if ($remove_static_property && preg_match('/^(?:\s*(?:-+)?[\w\-]+\s*:[^;{}\[\]()"\']*;?)*\s*$/', $v)) {
            return '';
        }
        // + | --------------------------------------------------------------------
        // + | remove system transform and litteral 
        // + | remove : {sys:...}
        // + | 
        // + |
        $container = new RegexMatcherContainer;
        $container->begin('\{', '\}(\\s*;\\s*)?', 'litteral'); 
        $container->begin("(\"|')", "\\1", 'string');        
        $container->begin("\\(", "\\)", 'parenthese');       
        $container->match("\\s+",'white-space');
        $remove_global &&
        $container->begin('\\[','\\](\\s*;\\s*)?', 'global');
        if ($remove_static_property)
        {
            $container->begin("\[","\](\\s*;\\s*)", 'bracket'); 
            $container->match("(-+)?[\w\-]+\\s*:\\s*", 'property');
        }
        $lpos = 0;
        $n = ''; 
        $container->treat($v, function($g, & $pos, $v)use(& $n, & $lpos){
            switch($g->tokenID)
            {
                case 'litteral':
                case 'white-space': 
                    $n = trim($n.substr($v, $lpos, $g->from-$lpos));
                    if ($g->tokenID=='white-space'){
                        $n .=' ';
                    } 
                    $lpos = $pos;
                break;
                case 'string':
                    break;
                case 'global':
                    $tv = $g->value;
                    $tv = preg_replace("/\\s+/", " ", $tv); 
                    if (self::IsGlobalExpression(trim($tv, StringUtility::DEFAULT_TRIM_CHAR.'[];'))){
                        // + | skip data 
                        $n.= substr($v, $lpos, $g->from-$lpos); 
                    } else {
                        $n.= substr($v, $lpos, $g->from-$lpos).$tv; 
                    }
                    $lpos = $pos;
                    break;
                case 'property':
                    // + | remove properties. 
                    $bvalue = substr($v, $pos);
                    $container = new RegexMatcherContainer;
                    $container->match(';','end');
                    $container->appendStringDetection();
                    $container->begin('\[', '\]', 'square-bracket-end');
                    $cpos = -1;
                    $container->treat($bvalue, function($g, $pos, $data)use(& $cpos, $container){
                        switch($g->tokenID){
                            case 'end':
                                $cpos = $pos;
                                return true; 
                            case 'square-bracket-end':
                                $cpos = false;
                                return true;
                        }
                    });
                    if (is_int($cpos)){
                        if ($cpos==-1){
                            $cpos = $pos + strlen($bvalue)+1;
                        }else{
                            $cpos += $pos;
                        } 
                    } else if ($cpos === false) {
                        $ln = $container->getLastPosition();
                        $s = self::_TreatPropertyExpression($g->value).substr($bvalue, 0, $ln);
                        $s = self::_TreatExpression($s);
                        $n .= substr($v, $lpos, $g->from-$lpos).$s; 
                        $pos+=$ln;
                        $lpos = $pos;
                        return;
                    } else {
                        throw new IGKException('invalid data');
                    }
                    $n.= substr($v, $lpos, $g->from-$lpos);  
                    $lpos = $cpos;
                    $pos = $lpos;
                    break;
            }
        }); 
        $n.= substr($v, $lpos); 
        return trim($n); 
    }
    /**
     * treat value - remove 
     * @param string $value 
     * @return string 
     */
    public function treat(string $value){
        $s = $value;
        $s = self::RemoveTransformLitteralFrom($s, $this->treatGlobal, $this->removeStaticProps);   
        return $s;
    }
}