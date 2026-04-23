<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PhpHelper.php
// @date: 20220601 14:18:34
// @desc: PhpHelp
namespace IGK\Helper;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKType;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;

/**
* Php helper.
* @package IGK\Helper
*/
class PhpHelper{
    /**
     * get callable from string
     * @param mixed $full_class_method 
     * @param string $delimiter 
     * @return null|(string[]&callable) 
     */
    public static function GetCallable(string $full_class_method, $delimiter='@'){
        $g = explode($delimiter, $full_class_method);
        if (is_callable($g)){
            return $g;
        }
        return null;
    }
    /**
    * String to class constants.
    * @param string $data
    */
    public static function StringToClassConstants(string $data){
        return implode("\n", array_map(
            function($n){
                return "const ".StringUtility::SanitizeIdentifier($n)." = '".$n."';"; 
            }, explode("|", $data)
        ));
    }
    /**
     * get comment summary
     * @param string $phpDoc 
     * @return void 
     */
    public static function GetCommentSummary(?string $phpDoc=null){
        if (is_null($phpDoc)){
            return null;
        }
        $c = "";
        if (!empty($_doc = $phpDoc)){
            foreach(explode("\n", $_doc) as $ll){
                $ll = trim($ll);
                $ll = ltrim($ll,"/*");
                $ll = ltrim($ll,"*");
                $ll = ltrim($ll);
                if(strpos($ll,"@")===0)
                    break;
                $ll = preg_replace('/\\\\\s*$/', '', $ll);
                $c .= $ll; 
            }
        }
        $c = igk_str_rm_last($c, '*/', 1);
        return $c;
    }
    /**
    * auto generate doc.
    * @return string
    */
    public static function HtmlComponentDocumention(){
        require_once IGK_LIB_DIR . "/igk_html_func_items.php";
        $_fcs = get_defined_functions(true)["user"];
        $ln = strlen(IGK_FUNC_NODE_PREFIX);
        sort($_fcs);
        $o = ""; 
        foreach ($_fcs as $f) {
            if (!preg_match("/^".IGK_FUNC_NODE_PREFIX."/", $f)) {
                continue;
            }
            $p = strtolower(substr($f, $ln));
            $m = "";
            $r = "self";
            $ref = new ReflectionFunction($f);
            if ($params = $ref->getParameters()) {
                $m = implode(", ", array_filter(array_map(function ($p) {
                    $g = "";
                    $t = "";
                    if ($p->hasType()) {
                        if ($p->isOptional()) {
                            $t .= "?";
                        }
                        $t .= $p->getType()->getName() . " ";
                    }
                    $g .= self::getDefaultValue($p);
                    if ($p->isVariadic()) {
                        $t .= " ...";
                    }
                    return $t . "$".$p->name . $g;
                }, $params)));
            }
            $c = self::GetCommentSummary($ref->getDocComment());
            $o .= "@method {$r} ".$p."($m) {$c}\n"; 
        } 
        return $o;
    }
    /**
     * get param default value
     * @param ReflectionParameter $p 
     * @return string 
     * @throws ReflectionException 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     */
    private static function getDefaultValue(\ReflectionParameter $p){
        $g = '';
        if ($p->isDefaultValueAvailable()) {
            if ($p->isDefaultValueConstant()) {
                $g .= "= " . $p->getDefaultValueConstantName();
            } else {
                $gg = $p->getDefaultValue();
                if (is_array($gg)){
                    if (empty($gg)) {
                        $g.= "= []";
                    }else{
                        igk_die("default array ".implode("gg:", $gg));
                    }
                }else{
                    $g .= "= " .(is_null($gg) ? 'null' : "'" . $gg . "'");
                }
            }
        }
        return $g;
    }
    /**
    * auto generate doc.
    * @param array<\ReflectionParameter> $params
    * @return string
    */
    public static function GetParamerterDescription( array $params):string{
        $s = '';
        $sep = '';
        foreach($params as $p){
            $s.= $sep;
            if ($p->hasType()){
                if ($p->isOptional()){
                    $s.="?";
                }
                $r = IGKType::GetName($p->getType());
                if (!IGKType::IsPrimaryType($r)){
                    $s .= '\\';
                }
                $s .= $r.' '; 
            } 
            $s.=  '$'.$p->getName();
            $s.= self::getDefaultValue($p);
            $sep = ',';
        }
        return $s;
    }
}