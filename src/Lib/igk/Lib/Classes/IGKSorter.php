<?php
// @file: IGKSorter.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use function igk_resources_gets as __;


final class IGKSorter{
    var $asc, $key;
    ///<summary></summary>
    public function __construct(){
        $this->asc=true;
    }
    ///<summary></summary>
    ///<param name="tab" ref="true"></param>
    ///<param name="key"></param>
    ///<param name="asc" default="true"></param>
    ///<param name="funcname"></param>
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
    ///<summary></summary>
    ///<param name="tab" ref="true"></param>
    ///<param name="key" default="null"></param>
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
    ///<summary></summary>
    ///<param name="tab"></param>
    ///<param name="key"></param>
    ///<param name="asc" default="true"></param>
    public static function SortByDisplay($tab, $key, $asc=true){
        return self::__SortValue($tab, $key, $asc, "SortKeyValue");
    }
    ///<summary></summary>
    ///<param name="tab"></param>
    ///<param name="key"></param>
    ///<param name="asc" default="true"></param>
    public static function SortByValue($tab, $key, $asc=true){
        return self::__SortValue($tab, $key, $asc, "SortValue");
    }
    ///<summary></summary>
    ///<param name="a"></param>
    ///<param name="b"></param>
    public function SortKeyValue($a, $b){
        $k=$this->key;
        $s1=strtolower(__($a->$k));
        $s2=strtolower(__($b->$k));
        $i=strcmp($s1, $s2);
        return $i;
    }
    ///<summary></summary>
    ///<param name="a"></param>
    ///<param name="b"></param>
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
