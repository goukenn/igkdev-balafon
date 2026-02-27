<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NoDbActiveControllerTrait.php
// @date: 20221116 18:05:08
// @desc: 
namespace IGK\System\Controllers\Traits;
use IGK\System\Models\IModelDefinitionInfo;

/**
* Trait providing no db active controller functionality.
* @package IGK\System\Controllers\Traits
*/
trait NoDbActiveControllerTrait{

    /**
    * Returns Data Table Info.
    * @return ?IModelDefinitionInfo
    */
    public function getDataTableInfo(): ?IModelDefinitionInfo{
        return null;
    }

    /**
    * auto generate doc.
    */
    public function getDataTableName(): ?string{
        return null;
    }

    /**
    * Returns Use Data Schema.
    * @return bool
    */
    public function getUseDataSchema(): bool
    {
        return false;
    }

    /**
    * Returns Can Init Db.
    */
    public function getCanInitDb(){
        return false;
    }
}