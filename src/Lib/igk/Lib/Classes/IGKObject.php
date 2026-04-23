<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKObject.php
// @date: 20220803 13:48:54
// @desc: 

/**
* Represent the base IGK object class
*/
class IGKObject {
    /**
    * auto generate doc.
    * @param string $key
    */
    public function __get(string $key){
        if(method_exists($this, $fc = "get".ucfirst($key))){ 
            return call_user_func(array($this, $fc), array_slice(func_get_args(), 1));
        }
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function __set(string $name, $value){
        $this->_setIn($name, $value);
    }
    /**
    * display value
    */
    public function __toString(){
        return get_class($this);
    }
    /**
    * auto generate doc.
    */    public function __wakeup(){
        if(method_exists($this, 'registerHook')){
            call_user_func_array([$this, 'registerHook'], []);
        }
    }
    /**
    * auto generate doc.
    * @param mixed * $value
    */
    protected function _setIn($name, & $value){
        if(method_exists($this, $fc  = "set".ucfirst($name))){
            call_user_func(array($this, $fc), $value);
            return true;
        }
        return false;
    }
    /**
    * auto generate doc.
    * @param mixed $method
    */
    public function callEvent($event, $method){
        throw new IGKException(__METHOD__." Not implement");
    }
    /**
    * auto generate doc.
    * @param mixed $obj
    */
    public function CompareTo($obj){
        $g=$this->getCmpObj();
        $s=$obj->getCmpObj();
        $r=($g == $s);
        return $r;
    }
    /**
    * used to dispose and release element
    */
    public function dispose(){}
    /**
    * auto generate doc.
    */    protected function getCmpObj(){}
    /**
    * override this method to filter call of global method used to call internal function (protected)
    */
    public static function Invoke($instance, string $method, ?array $args=null){
        if(method_exists($instance, $method)){
            if($args == null){
                return $instance->$method();
            }
            else{
                return $instance->$method(...$args); 
            }
        }
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function regEvent($name, $value){
        throw new IGKException(__METHOD__." not implement");
    }
}