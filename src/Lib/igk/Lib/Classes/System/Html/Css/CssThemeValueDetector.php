<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssThemeValueDetector.php
// @date: 20241030 06:45:06
namespace IGK\System\Html\Css;

use Exception;
use IGK\Helper\StringUtility;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;

///<summary></summary>
/**
* 
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

    public function __construct(){
        $this->treatGlobal = true;
        $this->removeStaticProps = true;
    }
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
    private static function _TreatPropertyExpression(string $value){
        $v = preg_replace("/\s+/", "", $value);
        // $v = preg_replace("/\s*:\s*/", ":", $v); 
        return $v;
    }
    /**
     * - remove global transform litteral 
     * - preserve string expression 
     * @param string $v 
     * @param bool $remove_global remove global expression 
     * @return string 
     * @throws Exception 
     */
    public static function RemoveTransformLitteralFrom(string $v, 
        bool $remove_global=false,
        bool $remove_static_property=false)
    { 
        // + | --------------------------------------------------------------------
        // + | remove system transform and litteral 
        // + | remove : {sys:...}
        // + | 
        // + |
        $container = new RegexMatcherContainer;
        $container->begin('{', '}(\\s*;\\s*)?', 'litteral'); // - remove
        $container->begin("(\"|')", "\\1", 'string');        // - leave
        $container->begin("\\(", "\\)", 'parenthese');       // - leave
        $container->match("\\s+",'white-space');
        $remove_global &&
        $container->begin('\\[','\\](\\s*;\\s*)?', 'global');

        if ($remove_static_property)
        {
            $container->begin("\[","\](\\s*;\\s*)", 'bracket'); // skip bracket 
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
                    // detect end of property value definition  
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
                            // read to end 
                            $cpos = $pos + strlen($bvalue)+1;
                        }else{
                            $cpos += $pos;
                        } 
                    } else if ($cpos === false) {
                        // - + 
                        // detect brank on definition 
                        $ln = $container->getLastPosition();
                        $s = self::_TreatPropertyExpression($g->value).substr($bvalue, 0, $ln);
                        $s = self::_TreatExpression($s);
                        // treat s 
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