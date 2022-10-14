<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewVarExpression.php
// @date: 20221010 21:40:24
namespace IGK\System;


///<summary></summary>
/**
* 
* @package IGK\System
*/
class ViewVarExpression{
    var $name;
    var $value;
    public function __construct(string $name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }
    public function __get($name){
        return $this->value->$name;
    }
    public function __call($name, $arguments){
        return call_user_func_array([$this->value, $name], $arguments);
    }
    public function __toString()
    {
        return '8';
    }
}