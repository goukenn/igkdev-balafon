<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigArrayAccessTrait.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Configuration;

/**
* auto generate doc.
* @package IGK\System\Configuration
*/
trait ConfigArrayAccessTrait {

    /**
    * auto generate doc.
    * @param mixed $n
    * @return void
    */
    public function offsetUnset(mixed  $n): void{
        unset($this->m_configs->$n);
    }
     /**
    * 
    * @param mixed $n
    */
    public function offsetExists($n):bool{
        return isset($this->m_configs->$n);
    }
    /**
    * 
    * @param mixed $n
    */
    public function offsetGet(mixed $n):mixed{
        return igk_getv($this->m_configs, $n);
    }
     /**
    * 
    * @param mixed $n
    * @param mixed $v
    */
    public function offsetSet($n, $v):void{
        $this->m_configs->$n=$v;
    }
}