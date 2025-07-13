<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EventPropertyArrayAccessTrait.7.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;
trait EventPropertyArrayAccessTrait{
     /**
    * 
    * @param mixed $i
    */
    public function offsetExists($i){
        return false;
    }
    /**
    * 
    * @param mixed $i
    */
    public function offsetGet($key){
        $n='@__callback';
        if(isset($this->$n)){
            $fc=$this->$n;
            unset($this->$n);
            return $fc($this, $key);
        }
        return $this->_p;
    }
    /**
    * 
    * @param mixed $i
    * @param mixed $v
    */
    public function offsetSet($i, $v){
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
    * 
    * @param mixed $i
    */
    public function offsetUnset($i){
        $this->_p=[];
    }
}