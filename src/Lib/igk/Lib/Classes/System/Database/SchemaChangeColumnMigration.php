<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaChangeColumnMigration.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;

use IGK\Database\DbColumnInfo;
use IGK\Helper\Activator;
use IGK\System\Console\Logger;

class SchemaChangeColumnMigration extends SchemaMigrationItemBase
{
    protected $fill_properties = ["table", "column", 'tag'];
    // source column to restore
    var $columnInfo;
    private $columns;

    public function up()
    {
        if (!$this->columnInfo) {
            Logger::danger("missing column ");
            return;
        }
        if (!$this->columns) {
            Logger::danger("missing target column");
            return;
        }
        $table  = $this->table; 
        // if ('clUser_Id' == $this->columnInfo->clName){
        //     Logger::warn(__FILE__.":".__LINE__ . " change ... ");
        // }
        Logger::info(sprintf('change column - %s.%s - tag: [%s]', $table, $this->columnInfo->clName, $this->tag));
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $cinfo = $this->columns[0];
        if (empty($cinfo->clName))
            $cinfo->clName = $this->column;

        if (empty($cinfo->clName)) {
            igk_die('missconfiguration. change column migration missing column name ' . $tb);
        }

        try {
            if ($cinfo->clName != $this->column) {
                // rename first 
                $ctrl::db_rename_column($tb, $this->column, $cinfo->clName);
            }
            $ctrl::db_change_column($tb, $cinfo);
        } catch (\Exception $ex) {
            Logger::warn(sprintf('last query : %s', $ctrl->getDataAdapter()->getLastQuery()));
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

    protected function loadChilds($childs)
    {
        $this->columns = [];
        $ctrl = $this->getMigration()->controller;
        $tb = $this->table;
        if ($tb) {
            $tb = igk_db_get_table_name($tb, $ctrl);
            foreach ($childs as $c) {
                $cl = DbColumnInfo::CreateWithRelation(igk_to_array($c->Attributes), $tb, $ctrl, $tbrelation);
                $this->columns[] = $cl;
                $this->columnInfo = $cl;
                break;
            }
        }
    }
}
