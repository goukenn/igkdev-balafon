<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaRenameColumnMigration.php
// @desc: schema builder helper
// @date: 20210422 09:09:36
namespace IGK\System\DataBase;

use IGKEvents;

/**
 * rename column migration
 * @package IGK\System\DataBase
 */
class SchemaRenameColumnMigration extends SchemaMigrationItemBase{
    protected $fill_properties = ["table", "column","new_name"];

    public function up(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $g = $ctrl::db_rename_column($tb, $this->column, $this->new_name);
        igk_hook(IGKEvents::HOOK_DB_RENAME_COLUMN, ['column'=>$this->column, "name"=>$this->new_name]);
    }
    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl::db_rename_column($tb, $this->new_name, $this->column);
    }

}