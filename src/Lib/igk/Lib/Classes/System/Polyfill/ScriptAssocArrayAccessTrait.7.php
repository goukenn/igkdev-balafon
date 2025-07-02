<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ScriptAssocArrayAccessTrait.7.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Polyfill;

trait ScriptAssocArrayAccessTrait{
    /**
    * 
    * @param mixed $k
    */
    function offsetExists($k):bool{
        return isset($this->data[$k]);
    }
    /**
    * 
    * @param mixed $i
    */
    public function offsetGet($key){
        return igk_getv($this->data, $key);
    }
    /**
    * 
    * @param mixed $k
    * @param mixed $v
    */
    function offsetSet($k, $v):void{
        $this->data[$k]=$v;
        $this->store(1);        
    }
    /**
     * 
     * @param mixed $k
     */
    function offsetUnset($k):void{
        unset($this->data[$k]);        
        $this->store(1);        

    }

}