<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeContainerTrait.php
// @date: 20230331 20:20:11
namespace IGK\System\Html\Dom\Traits;

use IGK\System\Html\Dom\Factory;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom\Traits
*/
trait HtmlNodeContainerTrait{
    use ArrayAccessSelfTrait;
    var $host;

    public function getCanRenderTag()
    {
        return false;
    }
    function getRenderedChilds($options = null)
    {
        return [$this->host];
    }
    public function setAttribute($n, $value){
        $this->host->setAttribute($n, $value);
        return $this;
    } 
    public function setContent($n){
        $this->host->setContent($n);
        return $this;
    }
    public function setClass($v){
        $this->host->setClass($v);
        return $this;
    }

    function _access_OffsetSet($n, $v){
        $this->host->_access_OffsetSet($n, $v);
    }
    function _access_OffsetGet( $n){
        $g =   $this->host->_access_OffsetGet($n);
        return $g;
    }
    function _access_OffsetUnset( $n){
        $this->host->_access_OffsetUnset($n);
    }
    function _access_offsetExists( $n):bool{
        return $this->host->_access_offsetExists($n); 
    }
    // - + drop fix router context prefer update _add method 
    // public function __call($n, $arg){
    //     return $this->host->__call($n, $arg);
    // }  
    public function __call($n, $arg){
        if (method_exists($this->host , $n)){
            return call_user_func_array([$this->host,$n], $arg);
        }
        if ($r = Factory::InvokeOn($this->host, $this->host->getTagName(), $n, $arg)){
            return $r;
        } 
        return parent::__call($n, $arg); 
    }   

    public function getFlag($k, $default = null){
        return $this->host->getFlag($k, $default);
    }
    public function setFlag($k, $value){
        $this->host->setFlag($k, $value);
        return $this;
    }
    protected function _add($n, bool $force=false):bool{         
        return $this->host->_add($n, $force);
    }
}