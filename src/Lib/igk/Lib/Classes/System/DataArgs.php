<?php
// @author: C.A.D. BONDJE DOUE
// @file: DataArgs.php
// @date: 20230129 12:59:40
namespace IGK\System;

use ArrayAccess;
use ArrayIterator;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IteratorAggregate;
use Traversable;

///<summary></summary>
/**
* 
* @package IGK\System
*/
class DataArgs implements ArrayAccess, IteratorAggregate{
    use ArrayAccessSelfTrait;
    protected $_data; 
 
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->_data, 0);
    }

    public function _access_OffsetGet($index)
    {
        return igk_getv($this->_data, $index);
    }
    public function __get($name)
    {
        return igk_getv($this->_data, $name);
    }

    public function __construct($data)
    {
        $this->_data = $data;
    }
    /**
     * return the string result
     * @return string|false 
     */
    public function __toString()
    {
        return json_encode($this->_data);
    }

    public function __call($name, $arguments)
    {
        if (is_object($this->_data)) {
            return call_user_func_array([$this->_data, $name], $arguments);
        }
    }
}