<?php
// @file: IGKDynamicObject.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* Igkdynamic object.
*/
final class IGKDynamicObject extends IGKObject{

    /**
    * Property: properties.
    * @var mixed
    */
    private $m_properties;
    /**
     * Invokes a stored callable property dynamically by name.
     * @param string $name The name of the property to call.
     * @param array $arguments Arguments to pass to the callable.
     * @return mixed The return value of the callable, or null if not found.
     */

    public function __call($name, $arguments){
        if(isset($this->m_properties[$name])){
            return call_user_func_array($this->m_properties[$name], $arguments);
        }
        return null;
    }
    /**
     * Constructor.
     */

    public function __construct(){
        $this->m_properties=array();
    }
    /**
     * Returns a dynamic property value by name.
     * @param string $name The property name to retrieve.
     * @return mixed The property value, or the parent's result if not found.
     */

    public function __get($name){
        if(isset($this->m_properties[$name]))
            return $this->m_properties[$name];
        return parent::__get($name);
    }
    /**
     * Sets a dynamic property value by name.
     * @param string $name The property name to set.
     * @param mixed $v The value to assign.
     */

    public function __set($name, $v){
        if(!$this->_setIn($name, $v)){
            $this->m_properties[$name]=$v;
        }
    }
    /**
     * Returns a string representation of this dynamic object.
     * @return string The class name followed by a hash symbol.
     */

    public function __toString(){
        return __CLASS__."#";
    }
    /**
     * Initializes dynamic properties from an associative array or iterable.
     * @param iterable|null $data Key-value pairs to set as dynamic properties.
     */

    public function initProperties($data){
        if($data) foreach($data as $k=>$v){
            $this->m_properties[$k]=$v;
        }
    }
}
