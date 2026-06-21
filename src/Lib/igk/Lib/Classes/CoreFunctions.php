<?php
// @author: C.A.D. BONDJE DOUE
// @file: CoreFunctions.php
// @date: 20260619 18:02:44
namespace IGK;


/**
* 
* @package IGK
* @author C.A.D. BONDJE DOUE
*/
abstract class CoreFunctions{
    /**
     * 
     * @param string $name 
     * @param mixed $arguments 
     * @return string|null 
     */
    public static function __callStatic(string $name, $arguments)
    {
        static $fc_list;
        if (is_null($fc_list)){
            $fc_list = [];
        }
        if (isset($fc_list[$name])){
            return $name;
        }
        if (function_exists($name)){
            $fc_list[$name] = 1;
            return $name;
        }
        igk_die(sprintf('missing global function [%s]', $name));    
    }
}