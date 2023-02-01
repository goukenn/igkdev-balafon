<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaRenameTableMigration.php
// @date: 20230117 09:33:45
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
class SchemaRenameTableMigration extends SchemaMigrationItemBase{
    protected $fill_properties = ["table", "to"]; 
 


    public function up(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
    }
    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
    }
}