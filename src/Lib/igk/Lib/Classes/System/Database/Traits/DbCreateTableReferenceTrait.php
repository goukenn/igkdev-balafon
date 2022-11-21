<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbCreateTableReferenceTrait.php
// @date: 20221118 23:13:08
namespace IGK\System\Database\Traits;

use IGK\Database\DbModuleReferenceTable;

///<summary></summary>
/**
* 
* @package IGK\System\Database\Traits
*/
trait DbCreateTableReferenceTrait{
 /**
     * get or change a reference table table reference 
     * @param mixed $tables 
     * @return void 
     */
    public function getDataTablesReference(& $tables){
        $ctab = & $tables;       
        unset($tables);
        $cf = new DbModuleReferenceTable($this, $ctab, $ctab);        
        return $cf;
    }
}