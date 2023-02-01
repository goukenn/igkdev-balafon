<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKLangResDictionary.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\Resources;

use ArrayAccess;
use ArrayIterator;
use Closure;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IteratorAggregate;
use Traversable;

///<summary> use for key's language operation</summary>
/**
*  use for key's language operation
*/
final class IGKLangResDictionary implements ArrayAccess, IteratorAggregate{
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
        $i = strtolower($i);
        return isset($this->_f[$i]);
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    protected function _access_offsetGet($i){
        $i = strtolower($i);
        return igk_getv($this->_f, $i);
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
        $i = strtolower($i);
        $this->_f[$i]=$v;
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    protected function _access_offsetUnset($i){
        $i = strtolower($i);
        unset($this->_f[$i]);
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
    public function load(string $file){
        $fc = Closure::fromCallable(function(){
            extract ((array)func_get_arg(1));
            include(func_get_arg(0)); 
            return $l;
        })->bindTo(null);
        $this->_f = array_change_key_case($fc($file, ["l"=> $this->_f]), CASE_LOWER);        
    }
    public function set($key, $value){
        $this->_f[$key] = $value;
    }
    public function getIterator(): Traversable {
        return new ArrayIterator($this->_f);
    }
}