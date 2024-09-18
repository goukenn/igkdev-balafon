<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaRenameColumnMigration.php
// @desc: schema builder helper
// @date: 20210422 09:09:36
namespace IGK\System\Database;

use IGK\Database\DbSchemas;
use IGK\System\Console\Logger;
use IGK\System\Database\MySQL\BooleanQueryResult;
use IGKEvents;

/**
 * rename column migration
 * @package IGK\System\Database
 */
class SchemaRenameColumnMigration extends SchemaMigrationItemBase{
    protected $fill_properties = ["table", "column","new_name"];
    public function __construct($migration)
    {
        parent::__construct($migration);
    }
    public function up(){
        $ctrl = $this->getMigration()->controller;
        $table = $this->table;
        list($column,$new_name) = igk_extract($this, 'column|new_name');
        $tb = igk_db_get_table_name($table, $ctrl);
        $v_renamed = $ctrl::db_rename_column($tb, $column, $new_name);
        if ($v_renamed instanceof BooleanQueryResult ){
            // possibiliy to remove column from schema list 
            if ($v_renamed->success()){ 
                // contains db the remove column 
                // $clinfo = DbSchemas::GetTableColumnInfo($tb, $ctrl);
                // $p = array_keys((array)$clinfo);
                // if (strtolower($column) == strtolower($new_name)){
                //     // possibility the column was the same 
                //     igk_wln(__FILE__.":".__LINE__ , $table." : same data :::: ", $column, $new_name, );
                // }
            }
        }
        Logger::warn("rename column - ".$table." ".$column ." > ". $new_name);
        igk_hook(IGKEvents::HOOK_DB_RENAME_COLUMN, ['column'=>$column, "name"=>$new_name /*, "table"=>$table*/]);
    }
    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl::db_rename_column($tb, $this->new_name, $this->column);
    }

}