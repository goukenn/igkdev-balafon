<?php
// @author: C.A.D. BONDJE DOUE
// @file: HookNameTrait.php
// @date: 20231010 13:42:26
namespace IGK\System\Traits;
use IGK\System\IO\Path;
/**
* 
* @package IGK\System\Traits
*/
trait HookNameTrait{

    /**
    * auto generate doc.
    * @param string $name
    */
    public static function HookName(string $name){
        return Path::Combine(static::class, $name);
    }
}