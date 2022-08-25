<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Utility.php
// @date: 20220803 13:48:58
// @desc: utility helper

namespace IGK\Helper;

use Exception; 
use IGK\System\Http\RequestUtility;
use stdClass;

abstract class Utility {
    const ignore_empty_method = "ignore_empty";

    /**
     * use to filter data 
     * @param mixed $a 
     * @return object|array 
     */
    private static function ignore_empty($a){
        $g = array_filter((array)$a);        
        if (is_object($a)){
            $g = (object)$g;
        }
        return $g;

    }

    public static function PostCref(callable $callback, $valid=1, $method="POST"){
        if (igk_server()->method($method) && igk_valid_cref($valid)){
            return $callback();
        }
        return false;
    }
    /**
     * 
     * @param mixed $paramHandler 
     * @param mixed $requestName 
     * @param mixed $paramName 
     * @param bool $update 
     * @return mixed 
     */
    public static function RequestGet($paramHandler, $requestName, $paramName, $update=true){
       return RequestUtility::RequestGet(...func_get_args());
    }
    /**
     * get the email display
     * @param mixed $r 
     * @return string 
     */
    public static function GetUserEmailDisplay($r){
        return implode(" ",array_filter([
            strtoupper($r->clLastName), ucfirst($r->clFirstName),
            "&lt;".$r->clLogin."&gt;"]));
    }
    /**
     * get the user fullname display
     * @param mixed $r 
     * @return string 
     */
    public static function GetFullName($r){
        return implode(" ", array_filter([ strtoupper($r->clLastName), ucfirst($r->clFirstName)]));
    }
    /**
     * convert raw to json.
     * @param mixed $raw 
     * @param mixed|null $options , ignore_empty=1|0 , default_ouput='{}'
     * @return mixed 
     * @throws Exception 
     */
    public static function To_JSON($raw , $options=null, $json_option = 0){
        $ignoreempty = igk_getv($options, "ignore_empty", 0);
        $default_output = igk_getv($options, "default_ouput", "{}");
        if(is_string($raw)){
            $sraw = json_decode($raw);
            if (json_last_error() === JSON_ERROR_NONE){
                if (!$ignoreempty){
                    return $raw;
                }
                $raw = $sraw;
            }else 
            return $default_output;
        }  
        $tab = [["r"=>$raw, "t"=>new stdClass()]];
        $root = null;
        while($m = array_shift($tab)){            
            $c = $m["t"];
            $raw = $m["r"];
            $a = 0;
            if (!$root)
                $root = $c;
            if (is_object($raw) || (is_array($raw))){
             

                foreach($raw as $k=>$v){
                    $a = 0;
                    if ($ignoreempty &&  (($v === null) || ($v =="")))
                        continue;
                    $is_obj = is_object($v); 
                    // | check if to_array method exists to get the array 
                    if ($is_obj && method_exists($v, "to_array")){                       
                        $c->$k = new stdClass();
                        array_unshift($tab, ["r"=>$v->to_array(), "t"=>$c->$k]); 
                        continue;
                    }
                    if (($a = is_array($v)) || $is_obj){
                   
                        if ($a && !igk_array_is_assoc($v)){
                            if ($ignoreempty){
                                $v = array_map([self::class, self::ignore_empty_method] ,
                                    array_filter($v)); 
                            }                      
                            $c->$k = $v;
                            continue;
                        }
                        $c->$k = new stdClass();
                        array_unshift($tab, ["r"=>$v, "t"=>$c->$k, "f"=>$k]);
                    }else{
                        $c->$k = $v;
                    }
                } 
            }
        }
        return json_encode($root, $json_option);
    }

    /**
     * 
     * @param string $clasname 
     * @return string[] 
     */
    public static function GetStaticClassMethods(string $clasname){
        $tab = array_map(function($i)use($clasname){
            return $clasname."::".$i; 
         }, get_class_methods($clasname));        
        sort($tab);
        return $tab;
    }
}