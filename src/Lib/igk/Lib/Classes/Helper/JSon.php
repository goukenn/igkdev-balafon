<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSon.php
// @date: 20230103 23:37:50
namespace IGK\Helper;

use IGKException;
use stdClass;

///<summary></summary>
/**
 * helper to encode in json 
 * @package IGK\Helper
 */
class JSon
{
    const ignore_empty_method = "ignore_empty";
    const ignore_empty_with_empty_array_method = "ignore_empty_with_empty_array";

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
    private static function ignore_empty_with_empty_array($a){
        if (is_string($a)){
            return $a;
        }
        $g = (array)$a;
        $t = [];
        foreach($g as $m=>$v){
            if (is_null($v)){
                continue;
            }
            $t[$m] = $v;
        }  
        if (is_object($a)){
            $g = (object)$t;
        }else{
            igk_wln_e("kj", $t);
        }
        return $g;
    }
    /**
     * helper to encode to json 
     * @param mixed $raw 
     * @param mixed|null $options , ignore_empty=1|0 , default_ouput='{}'
     * @return mixed 
     * @throws Exception 
     * */
    public static function Encode($raw, $options = null, $json_option = JSON_UNESCAPED_SLASHES)
    {
        $ignoreempty = igk_getv($options, "ignore_empty", 0);
        $default_output = igk_getv($options, "default_ouput", "{}");
        $allow_empty_array = igk_getv($options, "allow_empty_array", 0);
        $method = self::ignore_empty_method;
        if ($allow_empty_array){
            $method = self::ignore_empty_with_empty_array_method;
        }
        if (is_string($raw)) {
            $sraw = json_decode($raw);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (!$ignoreempty) {
                    return $raw;
                }
                $raw = $sraw;
            } else
                return $default_output;
        }
        // + | backup raw temp 
        $is_array = is_array($raw);
        $tab = [["r" => $raw, "t" => new stdClass()]];
        $root = null;
        while ($m = array_shift($tab)) {
            $c = $m["t"];
            $raw = $m["r"];
            $a = 0;
            if (!$root)
                $root = $c;
            if (is_object($raw) || (is_array($raw))) {


                foreach ($raw as $k => $v) {
                   
                    $a = 0;
                    if ($ignoreempty &&  (($v === null) || ($v == "")))
                        continue;
                    $is_obj = is_object($v);
                    // | check if to_array method exists to get the array 
                    if ($is_obj && method_exists($v, "to_array")) {
                        $c->$k = new stdClass();
                        array_unshift($tab, ["r" => $v->to_array(), "t" => $c->$k]);
                        continue;
                    }
                    if (($a = is_array($v)) || $is_obj) {

                        if ($a && !igk_array_is_assoc($v)) {
                            if ($ignoreempty  ) {
                                $v = array_map(
                                    [self::class, $method],
                                    array_filter($v)
                                );
                            }
                            $c->$k = $v;
                            continue;
                        }
                        $c->$k = new stdClass();
                        array_unshift($tab, ["r" => $v, "t" => $c->$k, "f" => $k]);
                    } else {
                        $c->$k = $v;
                    }
                }
            }
        }
        if ($is_array && $root) {
            $root = (array)$root;
        }
        return json_encode($root, $json_option);
    }
}
