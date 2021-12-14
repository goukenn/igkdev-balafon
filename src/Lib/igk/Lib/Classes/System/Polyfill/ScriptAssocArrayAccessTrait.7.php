<?php

namespace IGK\System\Polyfill;

trait ScriptAssocArrayAccessTrait{
    ///<summary></summary>
    ///<param name="k"></param>
    /**
    * 
    * @param mixed $k
    */
    function offsetExists($k):bool{
        return isset($this->data[$k]);
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    public function offsetGet($key){
        return igk_getv($this->data, $key);
    }
    ///<summary></summary>
    ///<param name="k"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $k
    * @param mixed $v
    */
    function offsetSet($k, $v):void{
        $this->data[$k]=$v;
        $this->store(1);        
    }
    ///<summary></summary>
    ///<param name="k"></param>
    /**
     * 
     * @param mixed $k
     */
    function offsetUnset($k):void{
        unset($this->data[$k]);        
        $this->store(1);        

    }

}