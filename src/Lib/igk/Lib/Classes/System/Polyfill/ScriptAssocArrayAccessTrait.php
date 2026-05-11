<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ScriptAssocArrayAccessTrait.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;

/**
* Trait providing script assoc array access functionality.
* @package IGK\System\Polyfill
*/
trait ScriptAssocArrayAccessTrait{
    /**
    * auto generate doc.
    * @param mixed $k
    */
    function offsetExists($k):bool{
        return isset($this->data[$k]);
    }
    /**
    * auto generate doc.
    * @param mixed $key
    */
    public function offsetGet(mixed $key):mixed{
        return igk_getv($this->data, $key);
    }
    /**
    * auto generate doc.
    * @param mixed $k
    * @param mixed $v
    */
    function offsetSet($k, $v):void{
        $this->data[$k]=$v;
        $this->store(1);
    }
    /**
    * auto generate doc.
    * @param mixed $k
    */
    function offsetUnset($k):void{
        unset($this->data[$k]);
        $this->store(1);
    }
}