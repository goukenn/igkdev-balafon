<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaChangeColumnMigration.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Database;
use Exception;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbSchemas;
use IGK\Helper\Activator;
use IGK\Helper\Database;
use IGK\System\Caches\DBCaches;
use IGK\System\Console\App;
use IGK\System\Console\Logger;

/**
* Schema change column migration.
* @package IGK\System\Database
*/
class SchemaChangeColumnMigration extends SchemaMigrationItemBase
{
    /**
    * Property: fill properties.
    * @var mixed
    */
    protected $fill_properties = ["table", "column", 'tag'];
    /**
    * Property: column info.
    * @var mixed
    */
    var $columnInfo;
    /**
    * Property: columns.
    * @var mixed
    */
    private $columns;
    /**
    * .ctr
    * @param mixed $migrations
    */
    public function __construct($migrations)
    {
        parent::__construct($migrations);
    }
    /**
     * up migrate 
     * @return void 
     * @throws Exception 
     */
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
        Logger::info(App::Gets(APP::BLUE_B, '[ db: change column ] ') . sprintf(' - %s.%s - tag: [%s]', $table, $this->columnInfo->clName, $this->tag));
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $migration = $this->getMigration();
        $cinfo = $this->columns[0];
        if (empty($cinfo->clName))
            $cinfo->clName = $this->column;
        if (empty($cinfo->clName)) {
            igk_die('missconfiguration. change column migration missing column name ' . $tb);
        }
        $v_column = $this->column;
        if (($mig = $migration->migrationListener) instanceof ISchemaMigrationInfoListener){
            $v_defTable = $mig->getTableSchemaFileDefinition($tb);
            $v_prefix = $v_defTable->prefix;
            $cinfo->clName = Database::AutoPrefixColumn($cinfo->clName, $v_prefix);
            if($link = $cinfo->clLinkColumn){
                $ltab = $mig->getTableSchemaFileDefinition($cinfo->clLinkType);
                if ($prefix = igk_getv($ltab, 'prefix', '')){
                    $link = Database::AutoPrefixColumn( $link, $prefix); 
                }
                $cinfo->clLinkColumn = $link;
            }
            $v_column = Database::AutoPrefixColumn($v_column, $v_prefix);
        }
        try {
            if ($cinfo->clName != $v_column) {
                $ctrl::db_rename_column($tb, $v_column, $cinfo->clName);
            }
            $ctrl::db_change_column($tb, $cinfo);
        } catch (\Exception $ex) {
            Logger::warn(sprintf('last query : %s', $ctrl->getDataAdapter()->getLastQuery()));
            Logger::danger($ex->getMessage());
        }
    }
    /**
     * down migrate
     * @return void 
     */
    public function down()
    {
        if (!$this->columnInfo)
            return;
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $ctrl::db_change_column($tb, $this->columnInfo);
    }
    /**
     * load child definitions 
     * @param mixed $childs 
     * @return void 
     */
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