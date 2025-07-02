<?php
// @file: IGKAppMethod.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKAppMethod{
    const CALLABLE_FUNC=8;
    const CALLABLE_USER_FUNC=16;
    const CLASS_METHOD=2;
    const C_CALLABLEN=37;
    const C_CLASS=33;
    const C_IDN=38;
    const C_METHODN=34;
    const C_OBJN=35;
    const C_PEVN=36;
    const FUNCTION_METHOD=3;
    const METHNAME=32;
    const OBJECT_METHOD=1;
    const OBJECT_METHOD_CLOSURE=4;
    private $m_;
    private $_object;
    private $_class;
    public function __call($d, $v){
        igk_die("call ".$d);
    }
    private function __construct(){
        $this->m_=new IGKAppMethodFlag();
    }
    public function __set($n, $v){
        igk_die("setting ".$n);
    }
    public function __sleep(){
        $t=igk_reflection_get_member($this);
        if($this->m_ && $this->m_->isEmpty()){
            unset($t["\0".__CLASS__."\0m_"]);
        }
        return array_keys($t);
    }
    public function __toString(){
        $v_pattern=IGK_STR_EMPTY;
        $m=$this->m_;
        switch($this->getType()){
            case self::OBJECT_METHOD:
            return "IGKAppMethod[FOR OBJECT METHOD]";
            case self::CLASS_METHOD:
            return "IGKAppMethod[".$this->getType()."::".$this->_class."::".$m."]";
            case self::FUNCTION_METHOD:
            return "IGKAppMethod[".$this->getType()."::".$m."]";
            case self::OBJECT_METHOD_CLOSURE:$v_pattern="CLOSURE =&gt; ".$this->getType();
            break;
            case self::CALLABLE_FUNC:$v_pattern=$m;
            break;
            case self::CALLABLE_USER_FUNC:$v_pattern="CALLABLE USER FUNC";
            break;
        }
        return "IGKAppMethod[".$v_pattern. "]";
    }
    private function _typeToString(){
        switch($this->getType()){
            case self::OBJECT_METHOD:
            return "OBJECT_METHOD";
            case self::CLASS_METHOD:
            return "CLASS_METHOD";
            case self::FUNCTION_METHOD:
            return "FUNCTION_METHOD";
            case self::OBJECT_METHOD_CLOSURE:
            return "CLOSURE_METHOD";
            case self::CALLABLE_FUNC:
            return "CALLABLE";
            case self::CALLABLE_USER_FUNC:
            return "CALLABLE_USER_FUNC";
        }
        return "TYPEUNKNOW";
    }
    public static function Create($class_or_object, & $method, $event){
        $c=$class_or_object;
        $out=null;
        if(($method === null) && igk_is_callable($c)){
            $out=new IGKAppMethod();
            $out->setType(self::CALLABLE_USER_FUNC);
            $out->setCallable($c);
            $out->setClass($c);
            $out->setId(igk_callable_id($c));
        }
        else{
            if(is_object($c)){
                if(is_string($method)){
                    if(method_exists($c, $method)){
                        $out=new IGKAppMethod();
                        $out->setType(self::OBJECT_METHOD);
                        $out->setMethodName($method);
                        $out->setClass(get_class($c));
                        $out->setObject($c);
                    }
                    else if(is_callable($method)){
                        $out=new IGKAppMethod();
                        $out->setType(self::CALLABLE_FUNC);
                        $out->setMethodName($method);
                        $out->setClass(IGK_STR_EMPTY);
                        $out->setObject($c);
                    }
                }
            }
            else{
                if(class_exists($c)){
                    if(method_exists($c, $method)){
                        $out=new IGKAppMethod();
                        $out->setType(self::CLASS_METHOD);
                        $out->setMethodName($method);
                        $out->setClass($c);
                    }
                }
                else if(function_exists($c)){
                    $out=new IGKAppMethod();
                    $out->setType(self::FUNCTION_METHOD);
                    $out->setMethodName($method);
                }
            }
        }
        return $out;
    }
    public function getCallable(){
        return $this->m_->getFlag(self::C_CALLABLEN);
    }
    public function getClass(){
        return $this->m_->getFlag(self::C_CLASS);
    }
    public function getId(){
        return $this->m_->getFlag(self::C_IDN);
    }
    public function getIdKey(){
        $m=$this->getMethodName();
        switch($this->getType()){
            case self::OBJECT_METHOD:
                $o=$this->getObject();
            return get_class($o)."::!>".$m."@".spl_object_hash($o);
            case self::CLASS_METHOD:
            return $this->getClass()."::>".$m;
            case self::FUNCTION_METHOD:
            return $m;
            case self::CALLABLE_USER_FUNC:
            return $this->getId();
        }
        return null;
    }
    public function getMethodName(): string{
        return $this->m_->getFlag(self::C_METHODN);
    }
    public function getObject(){
        return $this->m_->getFlag(self::C_OBJN);
    }
    public function getParentEvent(){
        return $this->m_->getFlag(self::C_PEVN);
    }
    public function getType(){
        return $this->m_->getFlag(-1);
    }
    public function Invoke($sender, $args){
        try {
            $extra=array($sender, $args);
            $m=$this->getMethodName();
            $o=$this->getObject();
            switch($this->getType()){
                case self::CALLABLE_USER_FUNC:$c=$this->getCallable();
                if(igk_is_callback_obj($c)){
                    return igk_invoke_callback_obj(null, $c, $extra);
                }
                return call_user_func_array($c, $extra);
                case self::OBJECT_METHOD:
                if(method_exists(get_class($o), IGK_FUNC_CALL_IN_CONTEXT)){
                    return $o->call_incontext($m, $extra);
                }
                else
                    return call_user_func_array(array($o, $m), $extra);
                case self::CLASS_METHOD:$c=$this->getClass();
                return call_user_func_array(array($c, $m), $extra);
                case self::FUNCTION_METHOD:
                return call_user_func($m, $sender, $args);
                case self::CALLABLE_FUNC:
                if(function_exists($m)){
                    return $m($sender, $args);
                }
                else{
                    if($o && method_exists($this->_object, 'invokeInContext')){
                        return $o->invokeInContext($m, array($sender, $args));
                    }
                }
                break;
            }
        }
        catch(Exception $ex){
            igk_show_exception($ex);
            igk_wln("IGKAppMethod::Invoke exception raised Method:[".$this->_typeToString()." ; ".$m."]". $this->__toString());
            igk_exit();
        }
    }
    public function IsRegistered($tab, $event){
        if($tab == null)
            return false;
        $m=$this->getMethodName();
        if($this->getType() == self::CALLABLE_FUNC){
            foreach($tab as $v){
                if($v->getMethodName() == $m){
                    return true;
                }
            }
            return false;
        }
        $idkey=$this->getIdKey();
        foreach($tab as $v){
            if($v->getIdKey() === $idkey){
                igk_ilog_assert(!igk_sys_env_production(), "failed to register {$idkey} - key already in collection. ".$idkey);
                return true;
            }
        }
        return false;
    }
    public function match($class_or_object, $method){
        $_cl=$this->getClass();
        $m=$this->getMethodName();
        $_obj=$this->getObject();
        switch($this->getType()){
            case self::OBJECT_METHOD:
            return (($class_or_object === $_obj) && ($m == $method));
            case self::CLASS_METHOD:
            break;
            case self::FUNCTION_METHOD:
            return ($m == $method);
            case self::CALLABLE_FUNC:
            igk_die("match function :: ");
            return false;
            case self::CALLABLE_USER_FUNC:
            break;
        }
        return (($class_or_object === $_cl) && ($m == $method));
    }
    public function matchParam($paramname, $obj){
        return igk_getv($this->getClass()->clParam, $paramname) === $obj;
    }
    public function setCallable($n){
        $this->m_->setFlag(self::C_CALLABLEN, $n);
    }
    public function setClass($n){
        $this->m_->setFlag(self::C_CLASS, $n);
    }
    public function setId($n){
        $this->m_->setFlag(self::C_IDN, $n);
    }
    public function setMethodName($n){
        $this->m_->setFlag(self::C_METHODN, $n);
    }
    public function setObject($n){
        $this->m_->setFlag(self::C_OBJN, $n);
    }
    public function setParentEvent($n){
        $this->m_->setFlag(self::C_PEVN, $n);
    }
    public function setType($t){
        $this->m_->setFlag(-1, $t);
    }
}
