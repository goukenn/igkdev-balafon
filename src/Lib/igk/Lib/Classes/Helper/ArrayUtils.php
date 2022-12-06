<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayUtils.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Helper;


class ArrayUtils{
    /**
     * 
     * @param array $table 
     * @param mixed $property 
     * @return void 
     */
    public static function PrependAfterSearch(array & $array, $search){
        if (($index = array_search($search, $array))!==false){
            unset($array[$index]);
            array_unshift($array, $search);
        }
    }
    public static function FillKeyWithProperty(array & $table, $property){
        $t = [];
        foreach($table as $ak){
            $key = igk_getv($ak, $property);
            $t[$key] = $ak;
        }
        $table =  $t;
    }
    ///<sumamry> clear table</summary>
    public static function Clean (array & $table){
        $table = [];  
    }
    public static function ArgumentsMap($a){
        if (is_string($a)){
            return escapeshellarg($a);
        }
        if (is_numeric($a))
            return $a;
        if (is_array($a)){
            return var_export($a, true);
        }
    }
}