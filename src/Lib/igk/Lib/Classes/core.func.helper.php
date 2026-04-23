<?php
// @author: C.A.D. BONDJE DOUE
// @filename: core.func.helper.php
// @date: 20251121 10:41:27
// @desc: define fonction in IGK namespace
// + | --------------------------------------------------------------------
// + | 
// + |
namespace IGK;

if (!function_exists('typeof')){
    /**
     * type of definition 
     * @param mixed $o mixed object 
     * @return null|string 
     */
    function typeof($o): ?string{
        if (is_null($o)){
            return 'null';
        }
        if (is_object($o)){
            return get_class($o);
        }
        if (is_array($o)){
            return 'array';
        }
        if (is_string($o)){
            return 'string';
        }
        if (is_bool($o)){
            return 'bool';
        }
        if (is_int($o)){
            return 'int';
        }
        return null;
    }
}