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
    /**
    * Property: fill properties.
    * @var mixed
    */
    protected $fill_properties = ["table", "column","new_name"];
    /**
    * .ctr
    * @param mixed $migration
    */
    public function __construct($migration)
    {
        parent::__construct($migration);
    }
    /**
    * Up.
    */
    public function up(){
        $ctrl = $this->getMigration()->controller;
        $table = $this->table;
        list($column,$new_name) = igk_extract($this, 'column|new_name');
        $tb = igk_db_get_table_name($table, $ctrl);
        $v_renamed = $ctrl::db_rename_column($tb, $column, $new_name);
        Logger::warn("rename column - ".$table." ".$column ." > ". $new_name);
        igk_hook(IGKEvents::HOOK_DB_RENAME_COLUMN, ['column'=>$column, "name"=>$new_name /*, "table"=>$table*/]);
    }
    /**
    * Down.
    */
    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl::db_rename_column($tb, $this->new_name, $this->column);
    }
}