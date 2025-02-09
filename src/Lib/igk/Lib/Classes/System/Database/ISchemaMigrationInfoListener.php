<?php
// @author: C.A.D. BONDJE DOUE
// @file: ISchemaMigrationInfoListener.php
// @date: 20250124 14:25:37
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
interface ISchemaMigrationInfoListener{
    function getTableSchemaFileDefinition(string $tablename);
}