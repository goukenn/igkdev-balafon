<?php
// @author: C.A.D. BONDJE DOUE
// @file: DataArgs.php
// @date: 20230129 12:59:40
namespace IGK\System;
use ArrayAccess;
use ArrayIterator;
use Exception;
use IGK\System\Core\IProxyDataArgs;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Polyfill\JsonSerializableTrait;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
* readonly data argument
* @package IGK\System
*/
class DataArgs implements IProxyDataArgs, IteratorAggregate, JsonSerializable{
    use ArrayAccessSelfTrait;
    use JsonSerializableTrait;
    /**
    * Property: p data.
    * @var mixed
    */
    protected $p_data;
    /**
    * Json serialize.
    */
    public function _json_serialize(){ 
        return self::Extract($this);
    }
    /**
    * auto generate doc.
    * @return Traversable<mixed
    */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->p_data, 0);
    }
    /**
     * retrieve affected data
     * @return mixed 
     */
    public function getData(){
        return $this->p_data;
    }
    /**
    * Access offset get.
    * @param mixed $index
    */
    public function _access_OffsetGet($index)
    {
        return igk_getv($this->p_data, $index);
    }
    /**
    * Access offset exists.
    * @param mixed $n
    */
    public function _access_offsetExists($n){
        if (is_object($this->p_data))
            return isset($this->p_data->{$n});
        return isset($this->p_data[$n]);
    }
    /**
    * auto generate doc.
    * @param mixed $name
    * @return mixed
    */
    public function __get($name)
    {
        return igk_getv($this->p_data, $name);
    }
    /**
    * .ctr
    * @param mixed $data
    */
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
        if (is_numeric($this->p_data)){
            return $this->p_data;
        } else if (is_string($this->p_data)){
            return $this->p_data;
        }
        return json_encode($this->p_data);
    }
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments)
    {
        if (is_object($this->p_data)) {
            return call_user_func_array([$this->p_data, $name], $arguments);
        }
    }
    /**
     * mapt to Array object 
     * @param array $mapping_table 
     * @return array 
     * @throws Exception 
     */
    public function mapToArray(array $mapping_table, ?callable $treat_value = null){
        $c = [];
        foreach($mapping_table as $k=>$v){
            $tv = igk_getv($this->p_data, $k);
            $c[$v] = $treat_value ? $treat_value($tv, $k) : $tv;
        }
        return $c;
    }
    /**
    * Extracts.
    * @param mixed $raw
    */
    public static function Extract($raw){
        $c = $raw;
        while($c instanceof static){
            $c = $c->getData();
        }
        return $c;
    }
}