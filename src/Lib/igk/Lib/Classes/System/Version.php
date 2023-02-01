<?php
// @author: C.A.D. BONDJE DOUE
// @file: Version.php
// @date: 20230118 12:10:18
namespace IGK\System;


///<summary></summary>
/**
* 
* @package IGK\System
*/
class Version{
    var $major;
    var $minor=0;
    var $build=0;
    var $release=0;

    public static function Parse(string $version){
        $p = explode('.', $version);
        $check = true;
        // check that every item is a number
        foreach($p as $t){
            if (!is_numeric($t)){
                $check=false;
                break;
            }
        }
        if (!$check){
            return null;
        }        
        list($major, $minor, $build, $release) = self::GetArrayValue($p, 4);
        $o = new static;
        foreach(array_keys((array)$o) as $k){
            $o->$k = $$k;
        }
        return $o;
    }
    static function GetArrayValue($tab, $count){
        $o = [];
        $i = 0;
        while($count>0){
            $o[] = !is_null($c = igk_getv($tab, $i)) ? intval($c) : null;
            $count--;
            $i++;            
        }
        return $o;
    }
    public function __toString()
    {
        $tab = [];
        foreach(['release','build', 'minor','major'] as $k){
            if (!is_null($this->$k)||(count($tab)>0))
                array_unshift($tab, $this->$k ?? 0);    
        }
        return implode(".", $tab);
    }
}