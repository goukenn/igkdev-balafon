<?php
// @author: C.A.D. BONDJE DOUE
// @file: MapHelper.php
// @date: 20221120 17:53:28
namespace IGK\Helper;

use Closure;
use IGK\Mapping\IDataMapper;
use IGK\Models\ModelBase;
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

   /**
    * 
    * @param mixed|array|ModelBase $data data to mapper 
    * @param array|IDataMapper $map 
    * @return mixed 
    * @throws IGKException 
    */
    public static function Map($data, $map){
        $v_out = [];
        if ($data instanceof ModelBase){
            $data = $data->to_array();
        }
        if ($map instanceof IDataMapper){
            $obj = [];
            foreach($data as $k=>$v){
                list($key, $value) = $map->map($k, $v) ?? [null,null]; 
                if (null === $key) continue;
                if (key_exists($key, $obj)){
                    igk_die("mapper result already containt ".$key);
                }
                $obj[$key]= $value; 
            }
            return (object)$obj;
        }
        $flip = array_flip($map);
        $keys = array_keys($flip); 
        foreach($data as $r){
            $c = (object) array_fill_keys($keys,null);
            foreach($keys as $k){
                $c->$k = igk_getv($r, $flip[$k]);
            }
            $v_out[] = $c;
            
        }
        return $v_out;
    } 
}
