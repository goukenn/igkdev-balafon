<?php
namespace IGK\System\Polyfill;

trait CSSDefaultArrayAccess{
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    public function offsetExists($i){
        if(!isset($this->_[self::PROPERTIES])){
            return false;
        }
        return isset($this->_[self::PROPERTIES][$i]);
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    public function offsetGet($key){
        if(!isset($this->_[self::PROPERTIES])){
            return null;
        }
        return igk_getv($this->_[self::PROPERTIES], $key);
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
        $g=null;
        if(!isset($this->_[self::PROPERTIES]) || !is_array($this->_[self::PROPERTIES])){
            $g = [];
            $this->_[self::PROPERTIES] =  & $g;
        }
        $g= & $this->_[self::PROPERTIES];
        $g[$i]=$v;
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    public function offsetUnset($i){
        if(isset($this->_[self::PROPERTIES])){            
            unset($this->_[self::PROPERTIES][$i]);
        }
    }
}