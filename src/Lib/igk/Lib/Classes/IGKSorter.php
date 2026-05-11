<?php
// @file: IGKSorter.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use function igk_resources_gets as __;

/**
* Igksorter.
*/
final class IGKSorter{
    /**
    * Properties: asc, key.
    * @var mixed
    */
    var $asc, $key;
    /**
     * Constructor.
     */
    public function __construct(){
        $this->asc=true;
    }
    /**
    * Applies a named sort function to an array or sortable object by key.
    * @param mixed & $tab
    * @param mixed $tab The array or object to sort (by reference).
    * @param mixed $key The key to sort by.
    * @param bool $asc Whether to sort in ascending order.
    * @return mixed The sorted array or object.
    */
    private static function __SortValue(& $tab, $key, $asc, $funcname){
        $t=new IGKSorter();
        $t->key=$key;
        $t->asc=$asc;
        if(is_array($tab)){
            usort($tab, array($t, $funcname));
        }
        else{
            if(method_exists(get_class($tab), "SortValueBy")){
                $tab->SortValueBy($key, $asc, array($t, $funcname));
            }
        }
        return $tab;
    }
    /**
    * Sorts an array or sortable object using the current sorter settings.
    * @param mixed & $tab
    * @param mixed $tab The array or object to sort (by reference).
    */
    public function Sort(& $tab, $key=null){
        if(is_array($tab)){
            usort($tab, array($this, "SortValue"));
        }
        else{
            if(method_exists(get_class($tab), "SortValueBy")){
                $tab->SortValueBy($this->key);
            }
        }
        if($key){
            $b=array();
            foreach($tab as $v){
                $b[igk_getv($v, $key)
                ]=$v;
            }
            $tab=$b;
        }
    }
    /**
     * Sorts a dataset by a display (translated) key value.
     * @param mixed $tab The array or object to sort.
     * @param mixed $key The key to sort by.
     * @param bool $asc Whether to sort in ascending order.
     * @return mixed The sorted array or object.
     */
    public static function SortByDisplay($tab, $key, $asc=true){
        return self::__SortValue($tab, $key, $asc, "SortKeyValue");
    }
    /**
     * Sorts a dataset by a raw value key.
     * @param mixed $tab The array or object to sort.
     * @param mixed $key The key to sort by.
     * @param bool $asc Whether to sort in ascending order.
     * @return mixed The sorted array or object.
     */
    public static function SortByValue($tab, $key, $asc=true){
        return self::__SortValue($tab, $key, $asc, "SortValue");
    }
    /**
     * Compares two items by their translated display key value.
     * @param mixed $a The first item to compare.
     * @param mixed $b The second item to compare.
     * @return int Negative, zero, or positive comparison result.
     */
    public function SortKeyValue($a, $b){
        $k=$this->key;
        $s1=strtolower(__($a->$k));
        $s2=strtolower(__($b->$k));
        $i=strcmp($s1, $s2);
        return $i;
    }
    /**
     * Compares two items by one or more keys, respecting ascending/descending order.
     * @param mixed $a The first item to compare.
     * @param mixed $b The second item to compare.
     * @return int Negative, zero, or positive comparison result.
     */
    public function SortValue($a, $b){
        $tk=$this->key;
        if(is_string($tk))
            $tk=array($tk=>$this->asc);
        $i=0;
        $o=0;
        $op=0;
        foreach($tk as $k=>$asc){
            $s1=
            $s2=null;
            if(is_object($a) && is_object($b)){
                $s1=strtolower(igk_getv($a, $k, ''));
                $s2=strtolower(igk_getv($b, $k, ''));
            }
            else{
                $s1=strtolower(igk_getv($a, $k, ''));
                $s2=strtolower(igk_getv($b, $k, ''));
            }
            if(is_integer($s1) && is_integer($s2)){
                $i=strnatcmp($s1, $s2);
            }
            else
                $i=strcmp($s1, $s2);
            if(($i != 0) && (!$asc)){
                $i *= -1;
            }
            if($i == 0){
                break;
            }
            $o=$i;
            $op=1;
        }
        return $o;
    }
}