<?php

namespace IGK\System\Polyfill;

trait IteratorTrait{
    public function current(): mixed{
        return $this->_iterator_current();
    }
    public function key(): mixed{
        return $this->_iterator_key();
    }
    public function rewind():void {
        $this->_iterator_rewind();
    }
    public function valid():bool{
        return $this->_iterator_valid();
    }
    public function next():void{
        $this->_iterator_next();
    }
}