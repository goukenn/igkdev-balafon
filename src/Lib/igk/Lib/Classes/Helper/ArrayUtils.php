<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayUtils.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Helper;


class ArrayUtils{
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