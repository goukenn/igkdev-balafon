<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationModuleMethodChecker.php
// @date: 20230303 10:54:17
namespace IGK\Controllers;


///<summary></summary>
/**
* 
* @package IGK\Controllers
*/
class ApplicationModuleMethodChecker{
    private static $sm_initDocs = [];

    /**
     * 
     * @param mixed $module 
     * @param mixed $env_param 
     * @param mixed $args 
     * @return false 
     */
    public static function initDoc($module, $env_param, ...$args){
        $__name = $module->getName();
        if (!isset(self::$sm_initDocs[$__name])){
            self::$sm_initDocs[$__name] = [];
        }
        if (!in_array($env_param, self::$sm_initDocs)){
            self::$sm_initDocs[$__name] = $env_param;
        }
        return false;
    }
}