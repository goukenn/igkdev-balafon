<?php
// @author: C.A.D. BONDJE DOUE
// @file: UriPath.php
// @date: 20221124 12:54:37
namespace IGK\Helper;

use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\Helper
 */
class UriPath
{
    const ACTION_EXTENTION_PATTERN = '(_:verbs)?((/:function(/:params+)?)?)?(:query)?(;:options)?';
    /**
     * check url path key 
     * @param mixed $uri 
     * @param mixed $pattern system matcher pattern
     * @param string $key 
     * @return int|false 
     * @throws IGKException 
     */
    public static function CheckPath($uri, $pattern, $key = 'path')
    {
        $pattern = self::CreateMatcherPattern($pattern);
        $g = parse_url($uri);
        if ($p = igk_getv($g, $key)) { 
            return preg_match($pattern, $p);
        }
        return false;
    }
    /**
     * check uri route pattern
     * @param string $uri uri to check 
     * @param string $method method to check
     * @return int|false 
     * @throws IGKException 
     */
    public static function CheckActionExtend(string $uri, string $method){
        return self::CheckPath($uri, '^/'.ltrim($method, '/'). self::ACTION_EXTENTION_PATTERN);
    }

    /**
     * create a matcher patter according to definition
     * @param string $s 
     * @return string 
     */
    public static function CreateMatcherPattern(string $s, $marker='/')
    {
        $s = preg_replace_callback("#:(?P<name>([a-z0-9]+))\+?#i", 
            \igk_pattern_matcher_matchcallback::class,
            $s);
        $s = preg_replace_callback(
            "/\\$\$/i",
            function () {
                return "";
            },
            $s
        );
        $s = str_replace('/', '\/', $s);
        return $marker . $s . '$'.$marker."i";
    }
}
