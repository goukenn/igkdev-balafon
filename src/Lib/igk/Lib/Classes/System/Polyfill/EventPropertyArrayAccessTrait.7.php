<?php
// @author: C.A.D. BONDJE DOUE
// @filename: EventPropertyArrayAccessTrait.7.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Polyfill;
 

trait EventPropertyArrayAccessTrait{
     ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    public function offsetExists($i){
        return false;
    }
    ///<summary></summary>
    ///<param name="i"></param>
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
    ///<summary></summary>
    ///<param name="i"></param>
    ///<param name="v"></param>
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
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    public function offsetUnset($i){
        $this->_p=[];
    }
}