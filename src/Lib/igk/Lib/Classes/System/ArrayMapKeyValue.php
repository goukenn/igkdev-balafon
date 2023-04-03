<?php
// @author: C.A.D. BONDJE DOUE
// @file: ArrayMapKeyValue.php
// @date: 20230329 20:11:07
namespace IGK\System;

use Closure;

///<summary></summary>
/**
* 
* @package IGK\System
*/
class ArrayMapKeyValue{
    /**
     * map keys value and filter null
     * @param callable $listener 
     * @param mixed $array 
     * @return array 
     */
    public static function Map(callable $listener, array $array):array{
        $g = new static;
        $fc = Closure::fromCallable($listener)->bindTo($g);
        $r = [];
        foreach(array_keys($array) as $k){
            if (($g = $fc($k, $array[$k]))){
                list($kk, $vv) = $g;
                $r[$kk] = $vv;
            }
        }
        return $r;
    }
}