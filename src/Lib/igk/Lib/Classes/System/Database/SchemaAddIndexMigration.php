<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaAddIndexMigration.php
// @date: 20231222 16:48:41
namespace IGK\System\Database;
/**
* 
* @package IGK\System\Database
*/
class SchemaAddIndexMigration extends SchemaMigrationItemBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $fill_properties = ["table", "columns"];

    /**
    * auto generate doc.
    * @param string $table
    * @param mixed $columns
    */
    public function setup(string $table, $columns){
        $this->raw = get_defined_vars();
    }

    /**
    * auto generate doc.
    */
    public function up()
    {
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl->db_add_index($tb, $this->columns) ;// , $cl, $after);
    }

    /**
    * auto generate doc.
    */
    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl->db_drop_index($tb, $this->columns) ;// , $cl, $after);
    }
}