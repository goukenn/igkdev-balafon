<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKLangResDictionary.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Resources;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

///<summary> use for key's language operation</summary>
/**
*  use for key's language operation
*/
final class IGKLangResDictionary implements ArrayAccess{
    use ArrayAccessSelfTrait; 
    private $_f;
    ///<summary></summary>
    /**
    * 
    */
    public function __construct(){}
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    protected function _access_offsetExists($i){
        return isset($this->_f[strtolower($i)]);
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    protected function _access_offsetGet($i){
        return $this->_f[strtolower($i)];
    }
    ///<summary></summary>
    ///<param name="i"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $i
    * @param mixed $v
    */
    protected function _access_offsetSet($i, $v){
        $this->_f[strtolower($i)]=$v;
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    protected function _access_offsetUnset($i){
        unset($this->_f[strtolower($i)]);
    }
    ///<summary> get sorted keys</summary>
    /**
    *  get sorted keys
    */
    public function _access_sortKeys(){
        if(($this->_f == null) || (igk_count($this->_f) == 0)){
            return false;
        }
        $keys=array_keys($this->_f);
        igk_usort($keys, "igk_key_sort");
        return $keys;
    }
}