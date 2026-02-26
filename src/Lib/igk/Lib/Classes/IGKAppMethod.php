<?php
// @file: IGKAppMethod.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* auto generate doc.
*/
final class IGKAppMethod{

    /**
    * auto generate doc.
    * @var mixed
    */
    const CALLABLE_FUNC=8;

    /**
    * auto generate doc.
    * @var mixed
    */
    const CALLABLE_USER_FUNC=16;

    /**
    * auto generate doc.
    * @var mixed
    */
    const CLASS_METHOD=2;

    /**
    * auto generate doc.
    * @var mixed
    */
    const C_CALLABLEN=37;

    /**
    * auto generate doc.
    * @var mixed
    */
    const C_CLASS=33;

    /**
    * auto generate doc.
    * @var mixed
    */
    const C_IDN=38;

    /**
    * auto generate doc.
    * @var mixed
    */
    const C_METHODN=34;

    /**
    * auto generate doc.
    * @var mixed
    */
    const C_OBJN=35;

    /**
    * auto generate doc.
    * @var mixed
    */
    const C_PEVN=36;

    /**
    * auto generate doc.
    * @var mixed
    */
    const FUNCTION_METHOD=3;

    /**
    * auto generate doc.
    * @var mixed
    */
    const METHNAME=32;

    /**
    * auto generate doc.
    * @var mixed
    */
    const OBJECT_METHOD=1;

    /**
    * auto generate doc.
    * @var mixed
    */
    const OBJECT_METHOD_CLOSURE=4;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $_object;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $_class;
    /**
     * Intercepts inaccessible method calls and terminates execution.
     *
     * @param string $d Method name that was called.
     * @param array  $v Arguments passed to the method.
     * @return void
     */

    public function __call($d, $v){
        igk_die("call ".$d);
    }
    /**
     * Constructor.
     */
    private function __construct(){
        $this->m_=new IGKAppMethodFlag();
    }
    /**
     * Intercepts inaccessible property assignments and terminates execution.
     *
     * @param string $n Property name.
     * @param mixed  $v Value to set.
     * @return void
     */

    public function __set($n, $v){
        igk_die("setting ".$n);
    }
    /**
     * Returns the list of serializable property keys for this instance.
     *
     * @return array
     */

    public function __sleep(){
        $t=igk_reflection_get_member($this);
        if($this->m_ && $this->m_->isEmpty()){
            unset($t["\0".__CLASS__."\0m_"]);
        }
        return array_keys($t);
    }
    /**
     * Returns a human-readable string representation of this method descriptor.
     *
     * @return string
     */

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
    /**
     * Returns a string label for the current method type.
     *
     * @return string
     */
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
    /**
     * Creates an IGKAppMethod instance from a class/object, method, and event.
     *
     * @param mixed  $class_or_object Class name, object instance, or callable.
     * @param mixed  &$method         Method name or callable reference.
     * @param mixed  $event           Associated event.
     * @return IGKAppMethod|null
     */

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
    /**
     * Returns the callable associated with this method descriptor.
     *
     * @return mixed
     */

    public function getCallable(){
        return $this->m_->getFlag(self::C_CALLABLEN);
    }
    /**
     * Returns the class name associated with this method descriptor.
     *
     * @return mixed
     */

    public function getClass(){
        return $this->m_->getFlag(self::C_CLASS);
    }
    /**
     * Returns the unique identifier for this method descriptor.
     *
     * @return mixed
     */

    public function getId(){
        return $this->m_->getFlag(self::C_IDN);
    }
    /**
     * Returns a unique string key identifying this method within its context.
     *
     * @return string|null
     */

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
    /**
     * Returns the method name stored in this descriptor.
     *
     * @return string
     */

    public function getMethodName(): string{
        return $this->m_->getFlag(self::C_METHODN);
    }
    /**
     * Returns the object instance associated with this method descriptor.
     *
     * @return mixed
     */

    public function getObject(){
        return $this->m_->getFlag(self::C_OBJN);
    }
    /**
     * Returns the parent event associated with this method descriptor.
     *
     * @return mixed
     */

    public function getParentEvent(){
        return $this->m_->getFlag(self::C_PEVN);
    }
    /**
     * Returns the method type constant for this descriptor.
     *
     * @return mixed
     */

    public function getType(){
        return $this->m_->getFlag(-1);
    }
    /**
     * Invokes the represented method or callable with the given sender and arguments.
     *
     * @param mixed $sender The event sender.
     * @param mixed $args   The event arguments.
     * @return mixed
     */

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
    /**
     * Checks whether this method is already registered in the given tab for an event.
     *
     * @param array|null $tab   Collection of registered method descriptors.
     * @param mixed      $event The event to check registration for.
     * @return bool
     */

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
    /**
     * Returns true when this descriptor matches the given class/object and method name.
     *
     * @param mixed  $class_or_object Class name or object instance to match.
     * @param string $method          Method name to match.
     * @return bool
     */

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
    /**
     * Returns true when the class parameter matching the given name equals the object.
     *
     * @param string $paramname The parameter name to look up.
     * @param mixed  $obj       The object to compare against.
     * @return bool
     */

    public function matchParam($paramname, $obj){
        return igk_getv($this->getClass()->clParam, $paramname) === $obj;
    }
    /**
     * Sets the callable for this method descriptor.
     *
     * @param mixed $n The callable to store.
     * @return void
     */

    public function setCallable($n){
        $this->m_->setFlag(self::C_CALLABLEN, $n);
    }
    /**
     * Sets the class name for this method descriptor.
     *
     * @param mixed $n The class name to store.
     * @return void
     */

    public function setClass($n){
        $this->m_->setFlag(self::C_CLASS, $n);
    }
    /**
     * Sets the unique identifier for this method descriptor.
     *
     * @param mixed $n The identifier to store.
     * @return void
     */

    public function setId($n){
        $this->m_->setFlag(self::C_IDN, $n);
    }
    /**
     * Sets the method name for this descriptor.
     *
     * @param string $n The method name to store.
     * @return void
     */

    public function setMethodName($n){
        $this->m_->setFlag(self::C_METHODN, $n);
    }
    /**
     * Sets the object instance for this method descriptor.
     *
     * @param mixed $n The object to store.
     * @return void
     */

    public function setObject($n){
        $this->m_->setFlag(self::C_OBJN, $n);
    }
    /**
     * Sets the parent event for this method descriptor.
     *
     * @param mixed $n The parent event to store.
     * @return void
     */

    public function setParentEvent($n){
        $this->m_->setFlag(self::C_PEVN, $n);
    }
    /**
     * Sets the method type constant for this descriptor.
     *
     * @param mixed $t The type constant to store.
     * @return void
     */

    public function setType($t){
        $this->m_->setFlag(-1, $t);
    }
}