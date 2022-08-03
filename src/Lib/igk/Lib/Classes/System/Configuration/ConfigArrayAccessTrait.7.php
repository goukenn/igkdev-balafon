<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigArrayAccessTrait.7.php
// @date: 20220803 13:48:57
// @desc: 

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
  