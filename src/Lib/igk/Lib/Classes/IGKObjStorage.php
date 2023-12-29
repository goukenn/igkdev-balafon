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
class IGKObjStorage{
    private $m_init;
    private $m_storage = [];
    ///<summary></summary>
    ///<param name="tab" default="null"></param>
    public function __construct(?array $tab=null){
        if($tab && is_array($tab)){
            $this->m_init = true;
            foreach($tab as $k=>$v){
                $this->__set($k, $v);
            }
            $this->m_init = false;
        }
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public function __get($v){
        if(isset($this->m_storage[$v])){
            return $this->m_storage[$v];
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    public function __set($n, $v){
        if (!$this->m_init){
            if($v === null){
                unset($this->m_storage[$n]);
                return;
            }
        }
        $this->m_storage[$n]=$v;
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
