<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaChangeColumnMigration.php
// @date: 20220803 13:48:56
// @desc: 

 
namespace IGK\System\Database;

use IGK\Database\DbColumnInfo;
use IGK\Helper\Activator;
use IGK\System\Console\Logger;

class SchemaChangeColumnMigration extends SchemaMigrationItemBase{
    protected $fill_properties = ["table", "column"];
    private $column_info;
    // source column to restore
    var $columnInfo;
    private $columns;
    public function up(){
        Logger::info('change table - possibility if not empty maybe the new structure will not match');
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        if (empty($this->column_info->clName))
            $this->column_info->clName = $this->column;
        try{

                $ctrl::db_change_column($tb, $this->column_info); 
        } catch(\Exception $ex){
            Logger::danger($ex->getMessage());
        }
    }
    public function down()
    {
        if (!$this->columnInfo)
            return; 
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl::db_change_column($tb, $this->columnInfo);
    }

    protected function loadChilds($childs){
        $this->columns = [];
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        foreach($childs as $c){
             
                $cl = DbColumnInfo::CreateWithRelation(igk_to_array($c->Attributes), $tb, $ctrl, $tbrelation);           
             
            $this->columns[]=$cl; 
            $this->column_info = $cl;
            break;
        }  
        // TODO: To remove
        // $this->up();   
    }
}