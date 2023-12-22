<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaAddIndexMigration.php
// @date: 20231222 16:48:41
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
class SchemaAddIndexMigration extends SchemaMigrationItemBase{
    protected $fill_properties = ["table", "columns"];
    public function setup(string $table, $columns){
        $this->raw = get_defined_vars();
    }
    public function up()
    {

        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl->db_add_index($tb, $this->columns) ;// , $cl, $after);
    }
    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl->db_drop_index($tb, $this->columns) ;// , $cl, $after);
 
    }
}