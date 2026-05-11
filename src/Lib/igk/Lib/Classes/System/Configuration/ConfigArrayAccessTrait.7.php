<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigArrayAccessTrait.7.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Configuration;

/**
* Trait providing config array access functionality.
* @package IGK\System\Configuration
*/
trait ConfigArrayAccessTrait {
    /**
    * Offset unset.
    * @param mixed $n
    */
    public function offsetUnset($n){
        unset($this->m_configs->$n);
    }
    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function offsetExists($n){
        return isset($this->m_configs->$n);
    }
    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function offsetGet($n){
        return igk_getv($this->m_configs, $n);
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    */
    public function offsetSet($n, $v){
        $this->m_configs->$n=$v;
    }
}