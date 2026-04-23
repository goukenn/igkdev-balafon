<?php
// @author: C.A.D. BONDJE DOUE
// @file: ISchemaMigrationInfoListener.php
// @date: 20250124 14:25:37
namespace IGK\System\Database;

/**
* auto generate doc.
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
interface ISchemaMigrationInfoListener{
    /**
     * register table changed
     * @param string $table 
     * @return mixed 
     */
    function regDefTableChanged(string $table);
    /**
     * get host table information 
     * @param string $tablename 
     * @return mixed 
     */
    function getTableSchemaFileDefinition(string $tablename);
}