<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewVarExpression.php
// @date: 20221010 21:40:24
namespace IGK\System;
/**
* 
* @package IGK\System
*/
class ViewVarExpression{

    /**
    * Name of name.
    * @var mixed
    */
    var $name;

    /**
    * Property: value.
    * @var mixed
    */
    var $value;

    /**
    * .ctr
    * @param string $name
    * @param null|mixed $value
    */
    public function __construct(string $name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name){
        return $this->value->$name;
    }

    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments){
        return call_user_func_array([$this->value, $name], $arguments);
    }
}