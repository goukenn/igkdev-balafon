<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlFilterAttribute.php
// @date: 20221107 19:24:14
namespace IGK\System\Html;

use ArrayAccess;
use ArrayIterator;
use IGK\System\Html\Dom\Traits\ClassAndStyleOffsetTrait;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IteratorAggregate;
use Traversable;

///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
class HtmlFilterAttributeArray implements ArrayAccess, IteratorAggregate{
    use ClassAndStyleOffsetTrait {
        _access_OffsetSet as parent_trait_offsetSet;
    }
    use ArrayAccessSelfTrait;

    protected $m_attributes;

    
    public function __construct($tab=null){
        $this->m_attributes = new HtmlAttributeArray;
        if ($tab){
            foreach($tab as $k=>$v){
                $this->_access_OffsetSet($k, $v);
            }
        }
    }
    /**
     * get attribute array
     * @return array 
     */
    public function to_array():array{
        return $this->m_attributes->to_array();
    }
    /**
     * resolv traversable
     * @return Traversable 
     */
    public function getIterator(): Traversable {
        return new ArrayIterator($this->m_attributes->to_array());
     }
    public function _access_OffsetGet($name){
        return $this->m_attributes[$name];
    }
    public function _access_OffsetSet($name, $v){
        if (in_array($name, ["style", "class"]))
            $this->parent_trait_offsetSet($name, $v);
        else
            $this->m_attributes[$name] = $v;
    }
    protected function _access_OffsetUnset($n){
        unset($this->m_attributes[$n]);
    }
    protected function _access_offsetExists($n){        
        return isset($this->m_attributes[$n]);
    }
    public function count(){
        return $this->m_attributes->count();
    }

}