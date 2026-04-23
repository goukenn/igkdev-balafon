<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationModuleMethodChecker.php
// @date: 20230303 10:54:17
namespace IGK\Controllers;

/**
* 
* @package IGK\Controllers
*/
/**
* auto generate doc.
* @package IGK\Controllers
*/
class ApplicationModuleMethodChecker{
    /**
    * Property: init docs.
    * @var mixed
    */
    private static $sm_initDocs = [];
    /**
    * auto generate doc.
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