<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IteratorTrait.7.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Polyfill;

trait IteratorTrait{
    public function current(){
        return $this->_iterator_current();
    }
    public function key(){
        return $this->_iterator_key();
    }
    public function rewind(){
        return $this->_iterator_rewind();
    }
    public function next(){
        return $this->_iterator_next();
    }
    public function valid(){
        return $this->_iterator_valid();
    }
}