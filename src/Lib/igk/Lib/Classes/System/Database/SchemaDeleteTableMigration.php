<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaCreateTableMigration.php
// @date: 20220803 13:48:56
// @desc: 

 
namespace IGK\System\Database;

use IGK\Database\DbColumnInfo;

class SchemaDeleteTableMigration extends SchemaMigrationItemBase{
    protected $fill_properties = ["table", "column"];
 
    // source column to restore
 
    var $columns = [];
    public function up(){
    
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl->getDataAdapter()->dropTable($tb);
       
    }
    public function down()
    {  
        //restore current table definition
    }

    protected function loadChilds($childs){
        // @author: C.A.D. BONDJE DOUE
        // @filename: SchemaCreateTableMigration.php
        // @date: 20221112 09:59:48
        // @desc: load columns info
        
        $this->columns = [];
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        foreach($childs as $c){
            $cl = DbColumnInfo::CreateWithRelation(igk_to_array($c->Attributes), $tb, $ctrl, $tbrelation);           
            $this->columns[]=$cl; 
        }    
    }
}