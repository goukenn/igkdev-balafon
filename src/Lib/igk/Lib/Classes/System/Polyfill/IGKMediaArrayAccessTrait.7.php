<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKMediaArrayAccessTrait.7.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;

/**
* Trait providing igkmedia array access functionality.
* @package IGK\System\Polyfill
*/
trait IGKMediaArrayAccessTrait
{

    /**
    * auto generate doc.
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
    * auto generate doc.
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
    * auto generate doc.
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
    * auto generate doc.
    * @param mixed $n
    */
    public function offsetUnset($n){
        $g=$this->getFlag(self::DEFAULT_THEME);
        if($g){
            unset($g[$n]);
        }
    }
}