<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaMigrationListener.php
// @date: 20250124 15:23:18
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
class SchemaMigrationListener implements ISchemaMigrationInfoListener{
    /**
     * source controller 
     */
    var $controller;
    /**
     * source file 
     * @var ?string
     */
    var $file;
    /**
     * 
     * @var ?ISchemaMigrationLoadingList
     */
    var $definition;

    public function getTableSchemaFileDefinition(string $tablename) { 
        return igk_getv($this->definition->tables, $tablename);
    }


}