<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayUtils.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Helper;

use IGK\System\IO\StringBuilder;

class ArrayUtils{
    /**
     * self check array
     * @param mixed $value 
     * @return null|array 
     */
    public static function CheckArray($value){
		if ($value && !is_array($value)){
			return [$value];
		}
		return $value;
	}
    /**
     * 
     */
    public static function MergeWith(array & $source, array $array_to_merge){
        $source = array_merge($source, $array_to_merge ); 
    }
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

    /**
     * 
     * @param array $array 
     * @param string $filter_regex 
     * @param bool $merging 
     * @return array 
     */
    public static function MergeFilter(array $array, string $filter_regex, bool $merging=true){
        $conf = $array;
        $filter = $filter_regex; 
        $tab = array_filter(array_map(
            function($v, $i)use($filter){
                if (preg_match($filter, $i)){
                    return [$i=>$v];
                }
            },
            $conf,
            array_keys($conf),
        ));
        if ($merging){
            if (($t = count($tab))>1)
                $tab = array_merge(...$tab);
            else if ($t==1){
                $tab = array_shift($tab);
            }
        } 
        return $tab;
    }
    /**
     * append item not merge
     * @param mixed &$array 
     * @param array $items 
     * @return void 
     */
    public static function AppendArrayItems(& $array, array $items){
        foreach($items as $k){
            $array[] = $k;
        }
    }
}