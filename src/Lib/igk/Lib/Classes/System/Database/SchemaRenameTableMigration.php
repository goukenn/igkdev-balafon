<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaRenameTableMigration.php
// @date: 20230117 09:33:45
namespace IGK\System\Database;
/**
* auto generate doc.
* @package IGK\System\Database
*/
class SchemaRenameTableMigration extends SchemaMigrationItemBase{
    /**
    * Property: fill properties.
    * @var mixed
    */
    protected $fill_properties = ["table", "to"];
    /**
    * Up.
    */
    public function up(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
    }
    /**
    * Down.
    */
    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
    }
}