<?php
// @author: C.A.D. BONDJE DOUE
// @file: Modules.php
// @date: 20241016 15:36:17
namespace IGK\System;

if (file_exists(__DIR__.'/auto_inc.modules.php'))
{
require_once(__DIR__.'/auto_inc.modules.php');
} else{
    interface auto_load_IModuleDefinition{
    }
    class_alias('auto_load_IModuleDefinition', 'IModuleDefinition');
}

///<summary></summary>
/**
* 
* @package IGK\System
* @author C.A.D. BONDJE DOUE
* @type {}
* @method static string info()
*/
abstract class Modules implements IModuleDefinition{
    public static function __callStatic($name, $arguments){
        return str_replace("_", "\\", $name);
    }
}

//Modules::