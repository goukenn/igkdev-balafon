<?php
// @author: C.A.D. BONDJE DOUE
// @file: HookNameTrait.php
// @date: 20260621 11:49:22
namespace IGK\Helper\Traits;

use IGKEvents;

/**
* 
* @package IGK\Helper\Traits
* @author C.A.D. BONDJE DOUE
*/
trait HookNameTrait{
    /**
     * 
     * @param string $path 
     * @return string 
     */
  static function HookName(string $path):string{
        return IGKEvents::CreateHookKey(static::class, $path);
    }
}