<?php
// @author: C.A.D. BONDJE DOUE
// @file: DataArgs.php
// @date: 20230129 12:59:40
namespace IGK\System;

use ArrayAccess;
use ArrayIterator;
use IGK\System\Core\IProxyDataArgs;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IteratorAggregate;
use Traversable;

///<summary></summary>
/**
* 
* @package IGK\System
*/
class DataArgs implements IProxyDataArgs, IteratorAggregate{
    use ArrayAccessSelfTrait;
    protected $p_data; 
 
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->p_data, 0);
    }

    public function getData(){
        return $this->p_data;
    }

    public function _access_OffsetGet($index)
    {
        return igk_getv($this->p_data, $index);
    }
    public function __get($name)
    {
        return igk_getv($this->p_data, $name);
    }

    public function __construct($data)
    {
        $this->p_data = $data;
    }
    /**
     * return the string result
     * @return string|false 
     */
    public function __toString()
    {
        return json_encode($this->p_data);
    }

    public function __call($name, $arguments)
    {
        if (is_object($this->p_data)) {
            return call_user_func_array([$this->p_data, $name], $arguments);
        }
    }
}