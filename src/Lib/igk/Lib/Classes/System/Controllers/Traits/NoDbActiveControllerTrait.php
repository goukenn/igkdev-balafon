<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NoDbActiveControllerTrait.php
// @date: 20221116 18:05:08
// @desc: 
namespace IGK\System\Controllers\Traits;
use IGK\System\Models\IModelDefinitionInfo;

/**
* auto generate doc.
* @package IGK\System\Controllers\Traits
*/
trait NoDbActiveControllerTrait{

    /**
    * auto generate doc.
    * @return ?IModelDefinitionInfo
    */
    public function getDataTableInfo(): ?IModelDefinitionInfo{
        return null;
    }
    /**
    * 
    */
    public function getDataTableName(): ?string{
        return null;
    }

    /**
    * auto generate doc.
    * @return bool
    */
    public function getUseDataSchema(): bool
    {
        return false;
    }

    /**
    * auto generate doc.
    */
    public function getCanInitDb(){
        return false;
    }
}