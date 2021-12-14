<?php

namespace IGK\System\Polyfill;

trait IteratorTrait{
    public function current(): mixed{
        return $this->_iterator_current();
    }
    public function key(): mixed{
        return $this->_iterator_key();
    }
}