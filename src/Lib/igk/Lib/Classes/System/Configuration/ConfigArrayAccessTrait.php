<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigArrayAccessTrait.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Configuration;

trait ConfigArrayAccessTrait {
    public function offsetUnset(mixed  $n): void{
        unset($this->m_configs->$n);
    }
     ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function offsetExists($n):bool{
        return isset($this->m_configs->$n);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function offsetGet(mixed $n):mixed{
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
    public function offsetSet($n, $v):void{
        $this->m_configs->$n=$v;
    }
}
  