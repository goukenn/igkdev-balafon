<?php

namespace IGK\System\Polyfill;

trait IteratorTrait{
    public function current(){
        return $this->_iterator_current();
    }
    public function key(){
        return $this->_iterator_key();
    }
}