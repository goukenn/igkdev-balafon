<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaAddColumnMigration.php
// @desc: schema builder helper
// @date: 20210422 09:09:36
namespace IGK\System\Database;

use IGK\Database\DbColumnInfo;
use IGK\System\Html\XML\XmlNode;
/**
 * update migrations
 * @package IGK\System\Database
 */
class SchemaAddColumnMigration extends SchemaMigrationItemBase{
    protected $fill_properties = ["table", "after"];

    
    /**
     * list of column info
     */
    protected $columns;
    protected function loadChilds($childs){
        $this->columns = [];
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        foreach($childs as $c){
            $tc = $c->getTagName();
            if (!empty($tc) && (strtolower($tc) == 'column')){
                $cl = DbColumnInfo::CreateWithRelation(igk_to_array($c->Attributes), $tb, $ctrl, $tbrelation);           
                $this->columns[]=$cl; 
            }
        }   
        
    }
    protected function checkRequirement()
    {
        if (empty($this->raw->table)){
            igk_die("missing 'table' property");
        }
    }
    public function up(){ 
        // igk_trace();
        // igk_wln_e("calling up");
        if ($this->table == 'tbigk_users')
        {
            igk_dev_wln("table : ". $this->table);
        }

        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        foreach($this->columns as $cl){
            if (is_null($cl->clName)){
                continue;
            }
            $ctrl->db_add_column($tb, $cl, $this->after);
        }
    }
    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        foreach($this->columns as $cl){
            $ctrl::db_rm_column($tb, $cl);
        }
    }
}