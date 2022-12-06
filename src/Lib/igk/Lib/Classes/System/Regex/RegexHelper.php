<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexHelper.php
// @date: 20221202 14:59:23
namespace IGK\System\Regex;

use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Regex
*/
class RegexHelper{
    /**
     * retrieve value from a regex
     * @param string $haystack where to search
     * @param string $pattern regex expression
     * @param string $name name to get value
     * @return null|string 
     * @throws IGKException 
     */
    public static function GetValue(string $haystack,string $pattern,string $name):?string{
        if (preg_match($pattern, $haystack, $match)){
            return igk_getv($match, $name);
        }
        return null;
    }
}