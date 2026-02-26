<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaDropColumnMigration.php
// @date: 20231222 17:35:47
namespace IGK\System\Database;
use IGK\Database\DbSchemas;
/**
 * 
 * @package IGK\System\Database
 */
class SchemaDropColumnMigration extends SchemaMigrationItemBase
{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $fill_properties = ["table", "column"];

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_cl;

    /**
    * auto generate doc.
    * @param string $table
    * @param mixed $column
    */
    public function setup(string $table, $column)
    {
        $this->raw = get_defined_vars();
    }

    /**
    * auto generate doc.
    */
    public function up()
    {
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl->db_drop_column($tb, $this->column);
        $this->m_cl = DbSchemas::GetTableColumnInfo($tb, $ctrl);
    }

    /**
    * auto generate doc.
    */
    public function down()
    {
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $cl = DbSchemas::GetTableColumnInfo($tb, $ctrl);
        $inf = igk_getv($cl, $this->column);
        $this->m_cl = $inf; 
    }
    /**
     * get column info
     * @return mixed 
     */

    protected function getCl(){
        return $this->m_cl;
    }
}