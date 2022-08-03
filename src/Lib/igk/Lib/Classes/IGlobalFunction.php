<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGlobalFunction.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK;

use IGKException;

/**
 * invoke global function with options.
 * @package 
 * @method static function igk_global_init_material(object $options); object with file property reference
 */
abstract class IGlobalFunction
{
    /**
     * check that global function exists
     * @param string $name 
     * @return bool 
     */
    public static function Exists(string $name){
        return function_exists($name);
    }
    /**
     * call gobal 
     * @param mixed $name 
     * @param mixed $args 
     * @return bool 
     * @throws IGKException 
     */
    public static function __callStatic($name, $args)
    {
        if (function_exists($name)) {
            $fc = $name;
            $result = call_user_func_array($fc, $args);
            if (is_object($o = igk_getv($args, 0))) {

                $o->handle = true;
                $o->result = $result;
            }
            return true;
        }
        return false;
    }
}
