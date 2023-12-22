<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaDropColumnMigration.php
// @date: 20231222 17:35:47
namespace IGK\System\Database;

use IGK\Database\DbSchemas;

///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
class SchemaDropColumnMigration extends SchemaMigrationItemBase{
    protected $fill_properties = ["table", "column"];
    private $m_cl; 
    public function setup(string $table, $column){
        $this->raw = get_defined_vars();
    }
    public function up(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl->db_drop_column($tb, $this->column);
        $this->m_cl = $cl = DbSchemas::GetTableColumnInfo($tb, $ctrl);
    }
        public function down(){
            $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $cl = DbSchemas::GetTableColumnInfo($tb, $ctrl);
        $inf = igk_getv($cl, $this->column);
        $this->m_cl = $inf;

        // $ctrl->db_add_column($tb, $cl);
    }
}