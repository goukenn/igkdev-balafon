<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaRenameTableMigration.php
// @date: 20230117 09:33:45
namespace IGK\System\Database;
/**
* 
* @package IGK\System\Database
*/
class SchemaRenameTableMigration extends SchemaMigrationItemBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $fill_properties = ["table", "to"];

    /**
    * auto generate doc.
    */
    public function up(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
    }

    /**
    * auto generate doc.
    */
    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
    }
}