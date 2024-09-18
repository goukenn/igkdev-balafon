<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssClassNameDetectorUtils.php
// @date: 20240913 09:47:11
namespace IGK\System\Html\Css;

use Exception;
use IGK\System\Text\RegexMatcherContainer;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
abstract class CssClassNameDetectorUtils{
    /**
     * 
     * @param CssClassNameDetector $detector 
     * @param string $filename 
     * @return mixed|void 
     */
    public static function DetectFromFile(CssClassNameDetector $detector, string $filename, & $references = null){
        if (!file_exists($filename)){
            return false;
        }
        $ext = igk_io_path_ext($filename);
        $p = ucfirst(strtolower($ext));
        $src = file_get_contents($filename);
        if (method_exists(static::class, $fc =  "DetectFrom".$p."Source")){
            return call_user_func_array([static::class, $fc], [$detector, $src, & $references]);
        } 
        return $detector->resolv($src);
    }
    /**
     * 
     * @param CssClassNameDetector $detector 
     * @param string $source 
     * @param mixed &$references 
     * @return array|void 
     * @throws Exception 
     */
    public static function DetectFromPhpSource(CssClassNameDetector $detector, string $source, & $references=null, $context=null){
        $g = token_get_all($source);
        $expression = [];
        $klist = sprintf("\b(%s)\b", strtolower(implode("|", igk_sys_get_html_components() ?? [])));
        $flag_defclass = false;
        while(count($g)>0){
            $e = array_shift($g);
            $v = $e;
            if (is_array($e)){
                $v = $e[1];
                $e = $e[0];
            }
            $n = is_int($e) ?  token_name($e) : '';
            switch($e){
                case T_CONSTANT_ENCAPSED_STRING:
                    $expression[] = $v;
                    $flag_defclass = false;
                    break;
                // case 262:
                //     if (preg_match("/(setClass)/i", $v)){
                //         $flag_defclass = true;
                //     }
                //     break;
                case 267:
                    self::DetectFromHtmlSource($detector, $v, $references);
                    break;
                case T_METHOD_C:
                    break;
                case T_STRING:
                    if (preg_match("/".$klist."/i", $v)){
                        $expression[] = 'igk-'.$v;
                        //$flag_defclass = true;
                    }else if (preg_match("/(setClass)/i", $v)){
                        $flag_defclass = true;
                    }
                    break;
                default:
                break;
            }
        }
        return $detector->resolv(implode("\n", array_unique($expression)), $references);
    }

    public static function DetectFromPHtmlSource(CssClassNameDetector $detector, string $source, & $references=null){
        return self::DetectFromPhpSource($detector, $source, $references, 'phtml');
    }
 

    /**
     * 
     * @param mixed $detector 
     * @param mixed $source 
     * @param mixed &$references 
     * @return mixed 
     * @throws Exception 
     */
    public static function DetectFromHtmlSource($detector , $source, & $references = null){        
        $container = new RegexMatcherContainer;
        $container->begin("<!--", "-->", "comment"); 
        $container->begin("<(script|style)", "</\\1\s*>", "ignore-tag"); 
        $container->begin("<(?:\\w+)", ">", "tag"); 
        $pos = 0;
        $src = $source;
        $match = [];
        while ($g = $container->detect($src, $pos)) {  
            $g = $container->end($g, $src, $pos);

            switch($g->tokenID ){
                case 'tag':
                    $def = (object)['ref'=>null];
                    $con = new RegexMatcherContainer;
                    $con->begin("\bclass(Name)?\b\s*", "=\s*",'class'); 
                    $con->begin('("|\')', "\\1", "value");  
                    $part = $con->extract($g->value, function($g)use($def){
                        if ($def->ref){
                            if ($g->tokenID =='value'){
                                $def->ref = null;
                                $g->value = preg_replace("/\s+/", " ", $g->value);
                                return true;
                            }
                        } else{
                            if ($g->tokenID=='class'){
                                $def->ref = true;
                            }
                        }
                        return false;
                    });
                    if ($part){
                        $match = array_merge($match, $part);
                    } 
                    break;
            } 
        }
        return $detector->resolv(implode(' ', $match), $references);
    }
}