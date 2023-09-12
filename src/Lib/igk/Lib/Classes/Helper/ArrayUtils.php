<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayUtils.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Helper;

use IGK\System\IO\StringBuilder;

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
    /**
     * 
     * @param mixed $a 
     * @return int|float|string|null|void 
     */
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


    /**
     * dump array 
     * @param array $array 
     * @return string 
     */
    public static function Export(array $array, $lf=PHP_EOL):string{
        $sb = new StringBuilder;
        $s = '';
        $ch = '';
        foreach($array as $k=>$v){
            $s .= $ch;
            if (is_numeric($k)){

            }else{
                $s .= igk_str_quotes($k)."=>";
            }
            $s.= $v.$lf;
            $ch = ',';            
        }
        $sb->set(sprintf('[%s]',$s));
        return ''.$sb;
    }
}