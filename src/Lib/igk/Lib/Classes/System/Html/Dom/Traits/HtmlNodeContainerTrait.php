<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeContainerTrait.php
// @date: 20230331 20:20:11
namespace IGK\System\Html\Dom\Traits;
use IGK\System\Html\Dom\Factory;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

/**
* 
* @package IGK\System\Html\Dom\Traits
*/
/**
* auto generate doc.
* @package IGK\System\Html\Dom\Traits
*/
trait HtmlNodeContainerTrait{
    use ArrayAccessSelfTrait;
    /**
    * Property: host.
    * @var mixed
    */
    var $host;
    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag()
    {
        return false;
    }
    /**
    * Returns Rendered Childs.
    * @param null|mixed $options
    */
    function getRenderedChilds($options = null)
    {
        return [$this->host];
    }
    /**
    * Sets Attribute.
    * @param mixed $n
    * @param mixed $value
    */
    public function setAttribute($n, $value){
        $this->host->setAttribute($n, $value);
        return $this;
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @return $this
    */
    public function setContent($n){
        $this->host->setContent(...func_get_args());
        return $this;
    }
    /**
    * Sets Class.
    * @param mixed $v
    */
    public function setClass($v){
        $this->host->setClass($v);
        return $this;
    }
    /**
    * Access offset set.
    * @param mixed $n
    * @param mixed $v
    */
    function _access_OffsetSet($n, $v){
        $this->host->_access_OffsetSet($n, $v);
    }
    /**
    * Access offset get.
    * @param mixed $n
    */
    function _access_OffsetGet( $n){
        $g =   $this->host->_access_OffsetGet($n);
        return $g;
    }
    /**
    * Access offset unset.
    * @param mixed $n
    */
    function _access_OffsetUnset( $n){
        $this->host->_access_OffsetUnset($n);
    }
    /**
    * Access offset exists.
    * @param mixed $n
    * @return bool
    */
    function _access_offsetExists( $n):bool{
        return $this->host->_access_offsetExists($n); 
    }
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $n
    * @param mixed $arg
    */
    public function __call($n, $arg){
        if (method_exists($this->host , $n)){
            return call_user_func_array([$this->host,$n], $arg);
        }
        if ($r = Factory::InvokeOn($this->host, $this->host->getTagName(), $n, $arg)){
            return $r;
        } 
        return parent::__call($n, $arg); 
    }
    /**
    * Returns Flag.
    * @param mixed $k
    * @param null|mixed $default
    */
    public function getFlag($k, $default = null){
        return $this->host->getFlag($k, $default);
    }
    /**
    * Sets Flag.
    * @param mixed $k
    * @param mixed $value
    */
    public function setFlag($k, $value){
        $this->host->setFlag($k, $value);
        return $this;
    }
    /**
    * Add.
    * @param mixed $n
    * @param bool $force
    * @return bool
    */
    protected function _add($n, bool $force=false):bool{         
        return $this->host->_add($n, $force);
    }
}