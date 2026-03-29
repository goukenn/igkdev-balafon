<?php
// @author: C.A.D. BONDJE DOUE
// @file: Regex.php
// @date: 20260204 09:30:05
namespace IGK\System\Text;
/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Text
*/
abstract class Regex{
    /**
    * auto generate doc.
    * @param mixed $default
    * @return void
    */
    public static function Get(string $key, $pattern, $haystack, $default= null){
        if (preg_match($pattern, $haystack, $tab)){
            return igk_getv($tab, $key, $default);
        }
        return $default;
    }
}