<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeContainerTrait.php
// @date: 20230331 20:20:11
namespace IGK\System\Html\Dom\Traits;

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
    public function __call($n, $arg){
        return $this->host->__call($n, $arg);
    }   
    public function getFlag($k, $default = null){
        return $this->host->getFlag($k, $default);
    }
    public function setFlag($k, $value){
        $this->host->setFlag($k, $value);
        return $this;
    }
}