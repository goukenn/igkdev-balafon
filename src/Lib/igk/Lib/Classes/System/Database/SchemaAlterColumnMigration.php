<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaAlterColumnMigration.php
// @date: 20220605 03:46:22
// @desc: alert table migration
namespace IGK\System\Database;
/**
 * alter table column
 * @package IGK\System\Database
 */
class SchemaAlterColumnMigration extends SchemaMigrationItemBase{

    /**
    * Property: fill properties.
    * @var mixed
    */
    protected $fill_properties = ["column"];

    /**
    * Up.
    */
    public function up(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl::db_alter_column($tb, $this->column, $this->new_name);
    }

    /**
    * Down.
    */
    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl::db_alter_column($tb, $this->new_name, $this->column);
    }
}