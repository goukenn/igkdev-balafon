<?php
namespace IGK\System\Configuration;
 

trait ConfigArrayAccessTrait {
    public function offsetUnset($n){
        unset($this->m_configs->$n);
    }
     ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function offsetExists($n){
        return isset($this->m_configs->$n);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function offsetGet($n){
        return igk_getv($this->m_configs, $n);
    }
     ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $v
    */
    public function offsetSet($n, $v){
        $this->m_configs->$n=$v;
    }
}
  