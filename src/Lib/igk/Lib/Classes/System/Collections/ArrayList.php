<?php
namespace IGK\System\Collections;

use ArrayAccess;
use Countable;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKIterator;
use IIGKArrayObject;
use Iterator;

class ArrayList implements ArrayAccess, Countable, IIGKArrayObject, Iterator{
    use ArrayAccessSelfTrait; 

    /**
     * access to array list 
     * @var array
     */
    protected $m_data = [];
    private $m_iterator;

    public function current() { 
        return $this->m_iterator->current();
    }

    public function next():void { 
        $this->m_iterator->next();
    }

    public function key() { 
        return $this->m_iterator->valid();
    }

    public function valid(): bool { 
        return $this->m_iterator->valid();
    }

    public function rewind():void { 
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
    public function to_array(){
        return $this->m_data;
    }
    public function reverse(){
        $this->m_data = array_reverse($this->m_data);
    }
   
    protected function _access_OffsetSet($n, $v){
        if ($v==null){
            if($n !== null){
                unset($this->m_data[$n]);
            }
        } else {
            if($n === null){
                //append 
                $this->m_data[] = $v;
            }else 
                $this->m_data[$n] = $v;
        }
    }
    protected function _access_OffsetGet($n){
        return igk_getv($this->m_data,$n);
    }
    protected function _access_OffsetUnset($n){
        unset($this->m_data[$n]);
    }

  
}