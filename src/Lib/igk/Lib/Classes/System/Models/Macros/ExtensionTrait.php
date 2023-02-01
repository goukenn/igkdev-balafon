<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExtensionTrait.php
// @date: 20230131 10:17:57
namespace IGK\System\Models\Macros;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* define abstract that need to be implement 
* @package IGK\System\Models\Macros
*/
trait ExtensionTrait{
    /**
     * get from guid
     * @param IGK\Models\ModelBase; $model 
     * @param string $guid 
     * @return ?IGK\Models\ModelBase return model
     */
    public abstract static function fromGuid(\IGK\Models\ModelBase $model, string $guid);
}