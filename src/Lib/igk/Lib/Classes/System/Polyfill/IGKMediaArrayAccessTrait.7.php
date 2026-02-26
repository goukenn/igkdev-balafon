<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKMediaArrayAccessTrait.7.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;

/**
* auto generate doc.
* @package IGK\System\Polyfill
*/
trait IGKMediaArrayAccessTrait
{
    /**
    * 
    * @param mixed $n
    */
    public function offsetExists($n){
        $g=$this->getFlag(self::DEFAULT_THEME);
        if($g){
            return isset($g[$n]);
        }
        return false;
    }
    /**
    * 
    * @param mixed $n
    */
    public function offsetGet($key){
        $g=$this->getFlag(self::DEFAULT_THEME);
        if($g){
            return igk_getv($g, $key);
        }
        return null;
    }
    /**
    * 
    * @param mixed $n
    * @param mixed $v
    */
    public function offsetSet($n, $v){
        $g=null;
        $g=& $this->getFlag(self::DEFAULT_THEME);
        if(!$g){
            $g=array();
            $this->_[self::DEFAULT_THEME]=& $g;
        }
        $g[$n]=$v;
    }
    /**
    * 
    * @param mixed $n
    */
    public function offsetUnset($n){
        $g=$this->getFlag(self::DEFAULT_THEME);
        if($g){
            unset($g[$n]);
        }
    }
}