<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewDataArgs.php
// @date: 20221113 08:48:43
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
/**
 * encapsulate data to pass to view
 * @package IGK\System\Console\Commands
 */
class ViewDataArgs implements ArrayAccess, IteratorAggregate
{
    use ArrayAccessSelfTrait;
    private $m_data;
    public static function GetData(ViewDataArgs $arg){
        return $arg->m_data;
    }
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->m_data, 0);
    }

    public function _access_OffsetGet($index)
    {
        return igk_getv($this->m_data, $index);
    }
    public function __get($name)
    {
        return igk_getv($this->m_data, $name);
    }

    public function __construct($data)
    {
        $this->m_data = $data;
    }
    /**
     * return the string result
     * @return string|false 
     */
    public function __toString()
    {
        return json_encode($this->m_data);
    }

    public function __call($name, $arguments)
    {
        if (is_object($this->m_data)) {
            return call_user_func_array([$this->m_data, $name], $arguments);
        }
    }
}
