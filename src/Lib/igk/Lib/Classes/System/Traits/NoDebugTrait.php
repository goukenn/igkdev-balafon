<?php
// @author: C.A.D. BONDJE DOUE
// @file: NoDebugTrait.php
// @date: 20240122 11:18:09
namespace IGK\System\Traits;

/**
* auto generate doc.
* @package IGK\System\Traits
* @author C.A.D. BONDJE DOUE
*/
trait NoDebugTrait
{
    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo()
    {
        return [];
    }
}