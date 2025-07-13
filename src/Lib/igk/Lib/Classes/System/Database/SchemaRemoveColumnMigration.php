<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaRemoveColumnMigration.php
// @desc: schema builder helper
// @date: 20210422 09:09:36
namespace IGK\System\Database;
/**
 * use to remove column
 * @package IGK\System\Database
 */
class SchemaRemoveColumnMigration extends SchemaMigrationItemBase{
    protected $fill_properties = ["table", "column"];
    public $columnInfo;
    protected function checkRequirement()
    {
        if (empty($this->raw->table)){
            igk_die("missing 'table' property");
        }
        if (empty($this->raw->column)){
            igk_die("missing 'column' property");
        }
    }
    public function up(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        if (method_exists($ctrl, 'db_rm_column')){
            $ctrl->db_rm_column($tb, $this->column);
        }else{
            $ctrl::db_rm_column($tb, $this->column);
        }
    }
    public function down(){ 
        $c_info = $this->columnInfo;
        if (!is_null($c_info)){
            $ctrl = $this->getMigration()->controller;
            $tb = igk_db_get_table_name($this->table, $ctrl);
            $ctrl::db_add_column($tb, $c_info, null); 
        }
    } 
}