<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IteratorTrait.7.php
// @date: 20220803 13:48:55
// @desc:
namespace IGK\System\Polyfill;
/**
* Trait providing iterator functionality.
* @package IGK\System\Polyfill
*/
trait IteratorTrait{
    /**
     * Returns the current element of the iterator.
     *
     * @return mixed
     */
    public function current(){
        return $this->_iterator_current();
    }
    /**
     * Returns the key of the current iterator element.
     *
     * @return mixed
     */
    public function key(){
        return $this->_iterator_key();
    }
    /**
     * Rewinds the iterator to the first element.
     *
     * @return mixed
     */
    public function rewind(){
        return $this->_iterator_rewind();
    }
    /**
     * Advances the iterator to the next element.
     *
     * @return mixed
     */
    public function next(){
        return $this->_iterator_next();
    }
    /**
     * Checks if the current iterator position is valid.
     *
     * @return mixed
     */
    public function valid(){
        return $this->_iterator_valid();
    }
}