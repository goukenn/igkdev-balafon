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
abstract class Regex{
    /**
     * 
     * @param string $key 
     * @param mixed $pattern 
     * @param mixed $haystack 
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