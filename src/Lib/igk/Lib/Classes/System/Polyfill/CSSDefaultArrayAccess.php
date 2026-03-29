<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CSSDefaultArrayAccess.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Polyfill;
use IGK\System\Html\Css\CssParser;
use IGK\System\Html\Css\CssUtils;
/**
* Trait providing cssdefault array access functionality.
* @package IGK\System\Polyfill
*/
trait CSSDefaultArrayAccess{
    /**
    * auto generate doc.
    * @param mixed $i
    */
    public function offsetExists($i):bool{
        if(!isset($this->_[self::PROPERTIES])){
            return false;
        }
        return isset($this->_[self::PROPERTIES][$i]);
    }
    /**
    * auto generate doc.
    * @param mixed $i
    */
    public function offsetGet(mixed $key):mixed{
        $tab = & $this->_[self::PROPERTIES];
        if(!isset($tab)){
            return null;
        }
        if (is_int($key) && $key === 0){ // auto added key is empty value
            if (key_exists('', $tab)){
                return $tab[''];
            } 
        }
        return igk_getv($tab, $key);
    }
    /**
    * auto generate doc.
    * @param ?string $v
    */
    public function offsetSet($i, $v):void{
        $g=null; $t_KEY = self::PROPERTIES;
        if(!isset($this->_[$t_KEY]) || !is_array($this->_[$t_KEY])){
            $g = [];
            $this->_[$t_KEY] =  & $g;
        }
        $g= & $this->_[$t_KEY];
        $this->_bindProperties($g, $i, $v); 
    }
    /**
    * auto generate doc.
    * @param mixed $i
    */
    public function offsetUnset($i):void{
        if(isset($this->_[self::PROPERTIES])){            
            unset($this->_[self::PROPERTIES][$i]);
        }
    }
}