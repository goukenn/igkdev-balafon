<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaAddForeignConstraintMigration.php
// @date: 20230227 13:41:24
// @desc: add foreign key column migration
namespace IGK\System\Database;
use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\System\Database\SchemaMigrationItemBase;
use IGKSysUtil;

/**
 * add foreign constraint 
 * @package IGK\System\Database
 */
class SchemaAddForeignConstraintMigration extends SchemaMigrationItemBase{
    /**
    * Property: fill properties.
    * @var mixed
    */
    protected $fill_properties = ["table", "from", "on", "columns", 'foreignKeyName'];
    /**
    * Up.
    */
    public function up(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        $from = igk_db_get_table_name($this->from, $ctrl);
        $v_ons = explode(',', $this->on);
        $v_columns = explode(',', $this->columns);
        $keyName = $this->foreignKeyName;
        $adapter = $ctrl->getDataAdapter();
        if ($v_ons && $v_columns && ( count($v_ons)==count($v_columns))){
            $inf = Activator::CreateNewInstance(SchemaForeignConstraintInfo::class, ['on'=>$this->on, 'from'=>$from,
            'columns'=>$this->columns,
            'foreignKeyName'=>$keyName
            ]);
            $v_foreignConstraints = [
                [$tb, $inf]
            ];
            if ($v_foreignConstraints){
                array_map(function($i)use($adapter){
                    list($tbname, $a) = $i;
                    $query = $adapter->getGrammar()->createAddConstraintReferenceForeignQuery($tbname, $a);
                    if (!$adapter->sendQuery($query)){
                        igk_ilog('failed to add reference : '.$query);
                    }
                }, $v_foreignConstraints);
            }
        }
    }
    /**
     * register the foreign constraint on the table definition. The query is sent by
     * Database::CreateTableFromSchema once the table is created
     * @param array &$tables loaded table definitions
     * @param BaseController $ctrl
     * @return void
     */
    public function doUpgrade(&$tables, BaseController $ctrl){
        $tb = IGKSysUtil::DBGetTableName($this->table, $ctrl);
        $info = igk_getv($tables, $tb);
        if (!$info){
            return;
        }
        if (count(explode(',', $this->on)) != count(explode(',', $this->columns))){
            igk_ilog('addForeignConstraint: `on` and `columns` must have the same number of columns - ' . $tb);
            return;
        }
        $inf = Activator::CreateNewInstance(SchemaForeignConstraintInfo::class, [
            'on' => $this->on,
            'from' => $this->from,
            'columns' => $this->columns,
            'foreignKeyName' => $this->foreignKeyName
        ]);
        $list = $info->foreignConstraint;
        if (empty($list)) {
            $list = [];
        } elseif (!is_array($list)) {
            $list = [$list];
        }
        $list[] = $inf;
        $info->foreignConstraint = $list;
    }
    /**
    * auto generate doc.
    * @return void
    */
    public function down(){
        igk_dev_wln_e(__FILE__.":".__LINE__ , "down....add constraint key .... not implement");
    }
}