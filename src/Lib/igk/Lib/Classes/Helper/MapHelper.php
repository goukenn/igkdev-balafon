<?php
// @author: C.A.D. BONDJE DOUE
// @file: MapHelper.php
// @date: 20221120 17:53:28
namespace IGK\Helper;

use Closure;
use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\Help
 */
class MapHelper
{
    /**
     * create a single field map callback
     * @param mixed $n 
     * @return Closure 
     */
    public static function Field($n)
    {
        return function ($a) use ($n) {
            return igk_getv($a, $n);
        };
    }
    /**
     * for mat map field
     * @param mixed $n 
     * @return Closure 
     */
    public static function Format($n)
    {
        return function ($a) use ($n) {
            $content = $n;
            foreach ($a as $k => $v) {
                $content = preg_replace_callback(
                    "#\{\{\s*" . $k . "\s*(\|\s*(?P<pipe>.+)?\}\}#",
                    function () use ($v) {
                        return $v;
                    },
                    $content
                );
            }
            return $content;
        };
    }

    /**
     * mapp data to object
     * @param mixed $data 
     * @param mixed $mapper 
     * @return null|object 
     * @throws IGKException 
     */
    public static function MapDataToObject($data, $mapper):?object
    {
        $rf = [];
        $keys = array_keys($mapper);
        $values = array_values($data);
        $i = 0;
        while (count($keys) > 0) {
            $q = array_shift($keys);
            $v = igk_getv($values, $i);
            if (is_numeric($q)) {
                $q = $mapper[$q];
            } else if (is_callable($fc = $mapper[$q])) {
                $v = $fc($v, $i);
            }
            $i++;
            $rf[$q] = $v;
        }
        if (!empty($rf)) {
            return (object)$rf;
        }
        return null;
    }
}
