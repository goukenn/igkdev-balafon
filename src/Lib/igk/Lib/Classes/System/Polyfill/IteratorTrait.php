<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IteratorTrait.php
// @date: 20220803 13:48:55
// @desc:
namespace IGK\System\Polyfill;

/**
* auto generate doc.
* @package IGK\System\Polyfill
*/
trait IteratorTrait{
    /**
     * Returns the current element of the iterator.
     *
     * @return mixed
     */
    public function current(): mixed{
        return $this->_iterator_current();
    }
    /**
     * Returns the key of the current iterator element.
     *
     * @return mixed
     */
    public function key(): mixed{
        return $this->_iterator_key();
    }
    /**
     * Rewinds the iterator to the first element.
     *
     * @return void
     */
    public function rewind():void {
        $this->_iterator_rewind();
    }
    /**
     * Checks if the current iterator position is valid.
     *
     * @return bool
     */
    public function valid():bool{
        return $this->_iterator_valid();
    }
    /**
     * Advances the iterator to the next element.
     *
     * @return void
     */
    public function next():void{
        $this->_iterator_next();
    }
}
