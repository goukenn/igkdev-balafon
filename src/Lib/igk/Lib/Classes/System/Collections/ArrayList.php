<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayList.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Collections;
use ArrayAccess;
use Countable; 
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Polyfill\IteratorTrait;
use IGKIterator;
use IGK\IArrayObject;
use Iterator;
/**
* Array list.
* @package IGK\System\Collections
*/
class ArrayList implements ArrayAccess, Countable, IArrayObject, Iterator{
    use ArrayAccessSelfTrait; 
    use IteratorTrait;
    /**
     * access to array list 
     * @var array
     */
    protected $m_data = [];
    // protected $preserveKey = false;
    /**
    * Property: iterator.
    * @var mixed
    */
    private $m_iterator;
    /**
    * Iterator current.
    */
    public function _iterator_current() { 
        return $this->m_iterator->current();
    }
    /**
    * Iterator next.
    * @return void
    */
    public function _iterator_next():void { 
        $this->m_iterator->next();
    }
    /**
    * Iterator key.
    */
    public function _iterator_key() { 
        return $this->m_iterator->key();
    }
    /**
    * Iterator valid.
    * @return bool
    */
    public function _iterator_valid(): bool { 
        return $this->m_iterator->valid();
    }
    /**
    * Iterator rewind.
    * @return void
    */
    public function _iterator_rewind():void { 
        $this->m_iterator = new IGKIterator($this->m_data);
        $this->m_iterator->rewind();
    }
    /**
    * Returns debug information for var_dump.
    */
    function __debugInfo()
    {
        return ["count"=>$this->count()];
    }
    /**
    * Clears.
    */
    public function clear(){
        $this->m_data = [];
    }
    /**
    * Returns count of.
    * @return int
    */
    public function count(): int{
        return count($this->m_data);
    }
    /**
    * To array.
    * @return ?array
    */
    public function to_array(): ?array{
        return $this->m_data;
    }
    /**
    * Reverse.
    */
    public function reverse(){
        $this->m_data = array_reverse($this->m_data);
    }
    /**
    * Access offset set.
    * @param mixed $n
    * @param mixed $v
    */
    protected function _access_OffsetSet($n, $v){
        if ($v===null){
            if($n !== null){
                unset($this->m_data[$n]);
            }
        } else {
            if($n === null){
                //append 
                $this->m_data[] = $v;
            }else {
                $this->m_data[$n] = $v;
            }
        } 
    }
    /**
    * Access offset get.
    * @param mixed $n
    */
    protected function _access_OffsetGet($n){
        return igk_getv($this->m_data,$n);
    }
    /**
    * Access offset unset.
    * @param mixed $n
    */
    protected function _access_OffsetUnset($n){
        unset($this->m_data[$n]);
    }
    /**
    * Access offset exists.
    * @param mixed $n
    */
    protected function _access_offsetExists($n){
        return isset($this->m_data[$n]);
    }
}