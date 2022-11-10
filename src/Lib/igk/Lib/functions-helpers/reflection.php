<?php

// @author: C.A.D. BONDJE DOUE
// @filename: reflection.php
// @date: 20220831 14:27:24
// @desc: reflection helpers function


///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_reflection_class_exists($name)
{
    if (!class_exists($name, false)) {
        igk_trace();
        igk_die("class [{$name}] doesn't exists");
    }
    return $name;
}
///<summary>get if object or classname is type of $name or extends it</summary>
/**
 * get if object or classname is type of $name or extends it
 */
function igk_reflection_class_extends($objOrClassName, $name)
{
    igk_reflection_class_exists($name);
    if ($objOrClassName) {
        $n = $objOrClassName;
        if (is_object($objOrClassName)) {
            $n = get_class($objOrClassName);
        }
        if ((strtolower($n) == strtolower($name)) || is_subclass_of($n, $name))
            return true;
    }
    return false;
}
///<summary>Represente igk_reflection_class_hierachi function</summary>
///<param name="type" type="ReflectionClass"></param>
/**
 * Represente igk_reflection_class_hierachi function
 * @param ReflectionClass $type 
 */
function igk_reflection_class_hierachi(ReflectionClass $type)
{
    $q = [];
    $q[] = $type->getName();
    while ($type = $type->getParentClass()) {
        $q[] = $type->getName();
    };
    return $q;
}
///<summary></summary>
///<param name="objOrClassName"></param>
///<param name="name"></param>
/**
 * 
 * @param mixed $objOrClassName 
 * @param mixed $name 
 */
function igk_reflection_class_implement($objOrClassName, $name)
{
    igk_reflection_interface_exists($name);
    if ($objOrClassName) {
        $n = $objOrClassName;
        if (is_object($objOrClassName)) {
            $n = get_class($objOrClassName);
        }
        if (!is_string($n) && !is_object($n)) {
            igk_ilog($n, __FUNCTION__);
            igk_die(__FUNCTION__ . ", can't get reflection class implementation");
        }
        $tab = class_implements($n);
        if (isset($tab[$name]))
            return true;
    }
    return false;
}
///<summary>check if class exists is an abstract class</summary>
/**
 * check if class name is an abstract class
 */
function igk_reflection_class_isabstract($name, $autoload = true)
{
    if (class_exists($name, $autoload)) {
        $v_rc = igk_sys_reflect_class($name);
        return $v_rc->isAbstract();
    }
    return -1;
}
///<summary>get reflection function arguments</summary>
/**
 * get reflection function arguments
 */
function igk_reflection_func_get_args($args)
{
    $callers = debug_backtrace();
    $f = igk_getv($callers[1], "function");
    $c = igk_getv($callers[1], "class");
    $tc = array();
    if ($c) {
        $m = new ReflectionMethod($c, $f);
        $i = 0;
        foreach ($m->getParameters() as $p) {
            $tc[$p->name] = igk_getv($args, $i);
            $i++;
        }
    }
    return $tc;
}


///<summary></summary>
///<param name="cl"></param>
/**
 * 
 * @param mixed $cl 
 */
function igk_reflection_get_constants($cl)
{
    $r = igk_sys_reflect_class($cl);
    return $r->getConstants();
}
///<summary>get reflexion properties. ignore dynamic data value</summary>
/**
 * get reflexion properties. ignore dynamic data value
 */
function igk_reflection_get_member($cl, $exclude_empty = 1)
{
    $c = get_class($cl);
    $t = array();
    $pc = $c;
    $exclude_func = function ($prop, $cl, &$value = null) use ($exclude_empty) {
        if ($exclude_empty) {
            $prop->setAccessible(true);
            $obj = $prop->getValue($cl);
            $prop->setAccessible(false);
            if (($obj == null) || empty($obj) || (is_object($obj) && method_exists($obj, "IsEmpty") && $obj->IsEmpty())) {
                return true;
            }
            $value = $obj;
        }
        return false;
    };
    while ($pc) {
        $r = igk_sys_reflect_class($pc);
        $tab = $r->getProperties(ReflectionProperty::IS_PRIVATE);
        foreach ($tab as $v) {
            $value = null;
            $prop = new ReflectionProperty($pc, $v->name);
            if ($prop->isStatic() || $exclude_func($prop, $cl, $value))
                continue;
            $t["\0" . $v->class . "\0" . $v->name] = $v->name;
        }
        $tab = $r->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($tab as $v) {
            $value = null;
            $prop = new ReflectionProperty($pc, $v->name);
            if ($prop->isStatic() || ($v->name[0] == "@") || $exclude_func($prop, $cl, $value))
                continue;
            $t[$v->name] = $v->name;
        }
        $r = $r->getParentClass();
        if ($r)
            $pc = $r->getName();
        else
            $pc = null;
    }
    return $t;
}


///<summary></summary>
///<param name="obj"></param>
/**
 * 
 * @param mixed $obj 
 */
function igk_reflection_getclass_var($obj)
{
    if (is_object($obj))
        return get_class_vars(get_class($obj));
    if (is_string($obj)) {
        if (class_exists($obj)) {
            return get_class_vars($obj);
        }
    }
    return null;
}
///<summary></summary>
///<param name="class"></param>
/**
 * 
 * @param mixed $class 
 */
function igk_reflection_getdeclared_filename($class)
{
    $h = igk_sys_reflect_class($class);
    return $h->getFileName();
}
///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name 
 */
function igk_reflection_interface_exists($name)
{
    if (!interface_exists($name))
        igk_die("class $name doesn't exists");
    return $name;
}