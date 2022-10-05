<?php
// @author: C.A.D. BONDJE DOUE
// @file: IDatabaseHost.php
// @date: 20220902 13:24:02
namespace IGK\System\Database;

use IGK\Models\DbModelDefinitionInfo;

///<summary>database host </summary>
/**
* database host 
* @package IGK\System\Database
*/
interface IDatabaseHost{
    /**
     * indicate if use data schema
     * @return bool 
     */
    function getUseDataSchema():bool;
    /**
     * indicate data adpater name to use
     * @return null|string 
     */
    function getDataAdapterName(): ?string;
    /**
     * return data definition. \
     * if getUseDataSchema() return false
     * @return null|DbModelDefinitionInfo 
     */
    function getDataTableInfo(): ?DbModelDefinitionInfo;
}