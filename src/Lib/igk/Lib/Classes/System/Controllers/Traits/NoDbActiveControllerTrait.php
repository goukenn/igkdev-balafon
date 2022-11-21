<?php

// @author: C.A.D. BONDJE DOUE
// @filename: NoDbActiveControllerTrait.php
// @date: 20221116 18:05:08
// @desc: 


namespace IGK\System\Controllers\Traits;

use IGK\Models\DbModelDefinitionInfo;

trait NoDbActiveControllerTrait{
    public function getDataTableInfo(): ?DbModelDefinitionInfo{
        return null;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDataTableName(): ?string{
        return null;
    }
    public function getUseDataSchema(): bool
    {
        return false;
    }
    public function getCanInitDb(){
        return false;
    }
}
