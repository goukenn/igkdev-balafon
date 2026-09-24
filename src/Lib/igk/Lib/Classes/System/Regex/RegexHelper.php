<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexHelper.php
// @date: 20221202 14:59:23
namespace IGK\System\Regex;

use IGK\Helper\IO;
use IGKException;

/**
 * auto generate doc.
 * @package IGK\System\Regex
 */
class RegexHelper
{
    /**
     * retrieve value from a regex
     * @param string $haystack where to search
     * @param string $pattern regex expression
     * @param string $name name to get value
     * @return null|string 
     * @throws IGKException 
     */
    public static function GetValue(string $haystack, string $pattern, string $name): ?string
    {
        if (preg_match($pattern, $haystack, $match)) {
            return igk_getv($match, $name);
        }
        return null;
    }
    /**
     * Escape.
     * @param string $value
     */
    public static function Escape(string $value)
    {
        $value = str_replace("/", "\\/", $value);
        return $value;
    }
    /**
     * treat and transform search path expression /** subdir * multiple 
     * @param array $content 
     * @return string[] regexs litteral 
     */
    public static function TreatSearchExpressions(array $content)
    {
        $tb = [];
        while (count($content) > 0) {
            $q = array_shift($content);
            if (igk_str_startwith($q, './')) {
                $q = substr($q, 2);
            }
            $end = false;
            $q = preg_replace('/(?!<\\\\)\//', '\\/', $q);
            $q = preg_replace('/(?!<\\\\)\\./', '\\.', $q);
            while (preg_match("/\{([^\\}]*)\}/", $q, $tab)) {
                $st = $tab[1];
                $st = sprintf('(%s)', str_replace(',', '|', $st));
                $q = str_replace($tab[0], $st, $q);
                $end = igk_str_endwith($q, $st);
            }
            if (!$end) {
                if (preg_match('/\.(.+)$/', $q, $tab)) {
                    $end = true;
                }
            }
            $q = str_replace('\/**', '(\/.+)?', $q);
            $q = str_replace('*', '.+', $q);
            if ($end) {
                $q .= '$';
            }
            

            $tb[] = sprintf('/%s/', $q);
        }
        return $tb;
    }

    /**
     * 
     * @param string $dir 
     * @param array $search expression tabs
     * @return null|string[] 
     */
    public static function SearchFrom(string $dir, array $search){
        $st = self::TreatSearchExpressions($search);
        $tb = IO::GetFiles($dir, function($f)use($st){
            while(count($st)>0){
                $q = array_shift($st);
                if (preg_match($q, $f)){
                    return true;
                }
            }
            return false;
        }, true);
        return $tb;
    }
}
