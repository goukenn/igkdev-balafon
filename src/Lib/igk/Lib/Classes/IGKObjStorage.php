<?php
// @file: IGKObjStorage.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
 * use to copy and retrieve data or null
 * @package 
 */
final class IGKObjStorage{
    ///<summary></summary>
    ///<param name="tab" default="null"></param>
    public function __construct(?array $tab=null){
        if($tab && is_array($tab)){
            foreach($tab as $k=>$v){
                $this->$k=$v;
            }
        }
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public function __get($v){
        if(isset($this->$v)){
            return $this->$v;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    public function __set($n, $v){
        if($v === null){
            unset($this->$n);
            return;
        }
        $this->$n=$v;
    }
    ///<summary>display value</summary>
    public function __toString(){
        return __CLASS__;
    }
    public function to_array(){
        $tab = (array)$this;        
        return $tab;
    }
    /**
     * return json data
     * @return string|false 
     */
    public function to_json(){
        return json_encode($this->to_array());
    }
}
