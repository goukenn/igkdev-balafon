<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewDataArgs.php
// @date: 20221113 08:48:43
namespace IGK\System;
use ArrayAccess;
use ArrayIterator;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Polyfill\JsonSerializableTrait;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
/**
 * 
 * @package IGK\System
 */
/**
 * encapsulate data to pass to view
 * @package IGK\System\Console\Commands
 */
class ViewDataArgs extends DataArgs implements ArrayAccess, IteratorAggregate, JsonSerializable
{
    use JsonSerializableTrait;
    public function __construct($data)
    {
        parent::__construct($data);
    }
    /**
     * 
     * @return mixed 
     */
    public function _json_serialize()
    {
        return json_encode($this->p_data);
    }
    public function __set(string $name, $args)
    {
        $this->p_data[$name] = $args;
    }
    public function isEmpty():bool{
        return empty($this->p_data);
    }
    /**
     * data
     * @param string $key key to get
     * @param mixed $def_value default value 
     * @return void 
     */
    public function get(string $key, $def_value=null){
        return igk_getv($this->p_data, $key, $def_value);
    }
}