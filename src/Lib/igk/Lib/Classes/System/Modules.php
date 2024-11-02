<?php
// @author: C.A.D. BONDJE DOUE
// @file: Modules.php
// @date: 20241016 15:36:17
namespace IGK\System;

// $f = Path::.'/.modules_dec.php';


///<summary></summary>
/**
* 
* @package IGK\System
* @author C.A.D. BONDJE DOUE
* @type {}
*/
class Modules{
    public static function __callStatic($name, $arguments){
        return str_replace("_", "\\", $name);
    }
}