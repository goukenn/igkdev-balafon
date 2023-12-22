<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaCreateTableMigration.php
// @date: 20220803 13:48:56
// @desc: 

 
namespace IGK\System\Database;

use IGK\Database\DbColumnInfo;

class SchemaDeleteTableMigration extends SchemaMigrationItemBase{
    protected $fill_properties = ["table"]; 
    // source column to restore
    /**
     * list of table to drop
     * @var array
     */
    var $tables = [];
    public function up(){    
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl->getDataAdapter()->dropTable($tb);       
    }
    public function down()
    {  
        //restore current table definition
        igk_die("c'ant restore");
    }

    /**
     * load childs 
     * @param mixed $childs 
     * @return void 
     */
    protected function loadChilds($childs){  
        $this->tables = [];
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        foreach($childs as $c){
            $cl = DbColumnInfo::CreateWithRelation(igk_to_array($c->Attributes), $tb, $ctrl, $tbrelation);           
            $this->tables[]=$cl; 
        }    
    }
}