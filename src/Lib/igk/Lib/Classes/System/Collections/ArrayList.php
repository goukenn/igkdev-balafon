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
use IIGKArrayObject;
use Iterator;

class ArrayList implements ArrayAccess, Countable, IIGKArrayObject, Iterator{
    use ArrayAccessSelfTrait; 
    use IteratorTrait;


    /**
     * access to array list 
     * @var array
     */
    protected $m_data = [];
    // protected $preserveKey = false;
    private $m_iterator;

    public function _iterator_current() { 
        return $this->m_iterator->current();
    }

    public function _iterator_next():void { 
        $this->m_iterator->next();
    }

    public function _iterator_key() { 
        return $this->m_iterator->key();
    }

    public function _iterator_valid(): bool { 
        return $this->m_iterator->valid();
    }

    public function _iterator_rewind():void { 
        $this->m_iterator = new IGKIterator($this->m_data);
        $this->m_iterator->rewind();
    }

    function __debugInfo()
    {
        return ["count"=>$this->count()];
    }
    public function clear(){
        $this->m_data = [];
    }
    public function count(): int{
        return count($this->m_data);
    }
    public function to_array(): ?array{
        return $this->m_data;
    }
    public function reverse(){
        $this->m_data = array_reverse($this->m_data);
    }
   
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
    protected function _access_OffsetGet($n){
        return igk_getv($this->m_data,$n);
    }
    protected function _access_OffsetUnset($n){
        unset($this->m_data[$n]);
    }

    protected function _access_offsetExists($n){
        return isset($this->m_data[$n]);
    }
}