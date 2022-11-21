<?php
// @author: C.A.D. BONDJE DOUE
// @file: MapHelper.php
// @date: 20221120 17:53:28
namespace IGK\Help;

use Closure;

///<summary></summary>
/**
* 
* @package IGK\Help
*/
class MapHelper{
    /**
     * create a single field map callback
     * @param mixed $n 
     * @return Closure 
     */
    public static function Field($n){
        return function($a)use($n){
            return igk_getv($a, $n);
        };
    }
    public static function Format($n){
        return function($a)use($n){
            $content = $n;
            foreach($a as $k=>$v){
                $content = preg_replace_callback(
                    "#\{\{\s*".$k."\s*\}\}#"
                    ,function()use($v){
                        return $v;
                    }
                    , $content); 
            }
            return $content;
        };
    }
}