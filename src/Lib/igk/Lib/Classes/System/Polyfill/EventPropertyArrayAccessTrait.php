<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EventPropertyArrayAccessTrait.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;
/**
* Trait providing event property array access functionality.
* @package IGK\System\Polyfill
*/
trait EventPropertyArrayAccessTrait{
    /**
    * auto generate doc.
    * @param mixed $i
    */
    public function offsetExists($i):bool{
        return false;
    }
    /**
    * auto generate doc.
    * @param mixed $i
    */
    public function offsetGet(mixed $key):mixed{
        $n='@__callback';
        if(isset($this->$n)){
            $fc=$this->$n;
            unset($this->$n);
            return $fc($this, $key);
        }
        return $this->_p;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function offsetSet($i, $v):void{
        $this->_n=$i;
        $this->_p=$v;
        $n='@__callback';
        if(isset($this->$n)){
            $fc=$this->$n;
            unset($this->$n);
            $fc($this);
        }
    }
    /**
    * auto generate doc.
    * @param mixed $i
    */
    public function offsetUnset($i):void{
        $this->_p=[];
    }
}