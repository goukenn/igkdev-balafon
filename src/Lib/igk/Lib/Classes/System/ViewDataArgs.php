<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewDataArgs.php
// @date: 20221113 08:48:43
namespace IGK\System;
use ArrayAccess; 
use IGK\Helper\JSon; 
use IGK\System\Polyfill\JsonSerializableTrait;
use IteratorAggregate;
use JsonSerializable; 

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

    /**
     * check if isset 
     * @param string $name 
     * @return bool 
     */
    public function __isset(string $name): bool{
        return is_array($this->p_data) ? isset($this->p_data[$name]) : isset($this->p_data);
    }
    /**
    * .ctr
    * @param mixed $data
    */
    public function __construct($data)
    {
        parent::__construct($data);
    }
    /**
    * auto generate doc.
    * @return mixed
    */
    public function _json_serialize()
    {
        return json_encode($this->p_data);
    }
    /**
    * destructor
    * @param string $name
    * @param mixed $args
    */
    public function __set(string $name, $args)
    {
        $this->p_data[$name] = $args;
    }
    /**
    * Returns true if Empty.
    * @return bool
    */
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
    /**
     * encode to json data expression 
     * @param mixed $options encode options object  
     * @param int $encode 
     * @return string|false 
     */
    public function to_json($options=null, int $encode= 0){
        return JSon::Encode($this->p_data, $options, $encode);
    }
    public function to_array(){
        return $this->p_data; 
    }
}