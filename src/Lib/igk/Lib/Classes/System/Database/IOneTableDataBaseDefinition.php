<?php
// @author: C.A.D. BONDJE DOUE
// @file: IOneTableDataBaseDefinition.php
// @date: 20220902 13:12:02
namespace IGK\System\Database;
use IGK\Database\IDbColumnInfo;
use IGK\System\Models\IModelDefinitionInfo;

/**
* auto generate doc.
* @package IGK\System\Database
*/
interface IOneTableDataBaseDefinition extends IDataBaseDefinition{
    /**
    * Returns Model Definition.
    * @return IModelDefinitionInfo
    */
    public function getModelDefinition(): IModelDefinitionInfo;
}