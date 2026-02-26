<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelMacrosTrait.php
// @date: 20251125 19:01:42
namespace IGK\System\Database\Traits;

use IGK\Models\ModelBase;

/**
 * 
 * @package IGK\System\Database\Traits
 * @author C.A.D. BONDJE DOUE
 */
trait ModelMacrosTrait
{

    /**
    * auto generate doc.
    */
    protected abstract function _GetAutoInsertDefinition();

    /**
    * auto generate doc.
    * @param ModelBase $model
    * @param mixed $name
    */
    public static function AutoInsertCache(ModelBase $model, $name)
    {
        $tab = static::_GetAutoInsertDefinition();      
        if ($r = igk_getv($tab, $name)) {
            $model::InsertIfNotExists($r);
        }
    }
}
