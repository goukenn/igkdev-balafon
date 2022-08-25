<?php
// @file: IGKObjectStrict.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKObjectStrict{
    private $m_ins;
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="params"></param>
    public function __call($n, $params){
        return null;
    }
    ///<summary></summary>
    private function __construct(){
    }
    ///<summary></summary>
    ///<param name="key"></param>
    public function __get($key){
        return igk_getv($this->m_ins, $key);
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    public function __set($key, $value){
        if(!isset($this->m_ins, $key))
            igk_die("setting of $key is not allowed");
        $this->m_ins[$key ]=$value;
    }
    ///<summary></summary>
    ///<param name="arraykey"></param>
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
