<?php
// @author: C.A.D. BONDJE DOUE
// @file: NoDebugTrait.php
// @date: 20240122 11:18:09
namespace IGK\System\Traits;


///<summary></summary>
/**
 * 
 * @package IGK\System\Traits
 * @author C.A.D. BONDJE DOUE
 */
trait NoDebugTrait
{
    public function __debugInfo()
    {
        return [];
    }
}
