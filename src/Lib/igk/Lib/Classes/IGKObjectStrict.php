<?php
// @file: IGKObjectStrict.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
final class IGKObjectStrict{
    private $m_ins;
    public function __call($n, $params){
        return null;
    }
    private function __construct(){
    }
    public function __get($key){
        return igk_getv($this->m_ins, $key);
    }
    public function __set($key, $value){
        if(!isset($this->m_ins, $key))
            igk_die("setting of $key is not allowed");
        $this->m_ins[$key ]=$value;
    }
    public static function Create($arraykey){
        if(is_array($arraykey) && igk_count($arraykey) > 0){
            $m=array();
            foreach($arraykey as $n){
                if(is_string($n))
                    $m[$n]=null;
            }
            if(igk_count($m) > 0){
                $g=new IGKObjectStrict();
                $g->m_ins=$m;
                return $g;
            }
        }
        return null;
    }
}