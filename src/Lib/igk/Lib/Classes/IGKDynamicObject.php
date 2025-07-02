<?php
// @file: IGKDynamicObject.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKDynamicObject extends IGKObject{
    private $m_properties;
    public function __call($name, $arguments){
        if(isset($this->m_properties[$name])){
            return call_user_func_array($this->m_properties[$name], $arguments);
        }
        return null;
    }
    public function __construct(){
        $this->m_properties=array();
    }
    public function __get($name){
        if(isset($this->m_properties[$name]))
            return $this->m_properties[$name];
        return parent::__get($name);
    }
    public function __set($name, $v){
        if(!$this->_setIn($name, $v)){
            $this->m_properties[$name]=$v;
        }
    }
    public function __toString(){
        return __CLASS__."#";
    }
    public function initProperties($data){
        if($data) foreach($data as $k=>$v){
            $this->m_properties[$k]=$v;
        }
    }
}
