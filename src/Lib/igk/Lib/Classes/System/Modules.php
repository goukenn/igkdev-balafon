<?php
// @author: C.A.D. BONDJE DOUE
// @file: Modules.php
// @date: 20241016 15:36:17
namespace IGK\System;
if (@file_exists(__DIR__.'/auto_inc.modules.php'))
{
    require_once(__DIR__.'/auto_inc.modules.php');
} else{
    /**
    * auto generate doc.
    * @package IGK\System
    */
    /**
    * auto generate doc.
    * @package IGK\System
    */
    /**
    * auto generate doc.
    * @package IGK\System
    */
    interface auto_load_IModuleDefinition{
    }
    class_alias('auto_load_IModuleDefinition', 'IModuleDefinition');
}
/**
* auto generate doc.
* @package IGK\System
* @author C.A.D. BONDJE DOUE
* @method static string info
*/
abstract class Modules implements IModuleDefinition{
    /**
    * Triggered when calling an inaccessible or undefined static method.
    * @param mixed $name
    * @param mixed $arguments
    */
    public static function __callStatic($name, $arguments){
        return str_replace("_", "\\", $name);
    }
}
//Modules::