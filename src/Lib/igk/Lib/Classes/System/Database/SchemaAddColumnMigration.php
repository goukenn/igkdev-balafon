<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaAddColumnMigration.php
// @desc: schema builder helper
// @date: 20210422 09:09:36
namespace IGK\System\Database;
use Exception;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbSchemas;
use IGK\Database\IDbColumnInfo;
use IGK\Helper\Database;
use IGK\System\Caches\DBCaches;
use IGKException;
/**
 * update migrations
 * @package IGK\System\Database
 */
class SchemaAddColumnMigration extends SchemaMigrationItemBase{

    /**
    * Property: fill properties.
    * @var mixed
    */
    protected $fill_properties = ["table", "after"];
    /**
     * list of column info
     */
    protected $columns;

    /**
    * Loads Childs.
    * @param mixed $childs
    */
    protected function loadChilds($childs){
        $v_table = $this->table;
        $this->columns = [];
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($v_table, $ctrl);
        foreach($childs as $c){
            $tc = $c->getTagName();
            if (!empty($tc) && (strtolower($tc) == 'column')){
                $attr = igk_to_array($c->Attributes);
                $cl = DbColumnInfo::CreateWithRelation($attr, $tb, $ctrl, $tbrelation);           
                $this->columns[]=$cl; 
            }
        }   
    }

    /**
    * Checks Requirement.
    */
    protected function checkRequirement()
    {
        if (empty($this->raw->table)){
            igk_die("missing 'table' property");
        }
    }

    /**
    * Sets up.
    * @param string $table
    * @param IDbColumnInfo $column
    * @param null|string $after
    */
    public function setup(string $table, IDbColumnInfo $column, ?string $after=null){
        $this->raw = (object)['table'=>$table, 'after'=>$after];
        $this->columns = [$column];
    }
    /**
     * 
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     */

    public function up(){ 
        $v_table = $this->table;
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($v_table, $ctrl);
        $after = $this->after;
        $prefix = '';
        if ($ref = DBCaches::GetTableInfo($tb)){
            $prefix = $ref->prefix;
        }
        if ($after)
            $after = Database::AutoPrefixColumn($after, $prefix);
        $changed = false;
        foreach($this->columns as $cl){
            if (is_null($cl->clName)){
                continue;
            }
            $hb = $cl->clName;
            $cl->clName = Database::AutoPrefixColumn($hb, $prefix);
            $ctrl->db_add_column($tb, $cl, $after);
            if ($cl->clIsIndex){
                $ctrl->db_add_index($tb, $cl->clName);
            } 
            if ($after){ // continue after
                $after = $cl->clName;
            }
            $changed = $changed || $cl->clIsUniqueColumnMember;
        }
        if ($migListerner = $this->getMigrationInfoListener()){
            $migListerner->regDefTableChanged($tb);
        }
    }
    /**
     * 
     * @return void 
     */

    public function down(){
        $ctrl = $this->getMigration()->controller;
        $tb = igk_db_get_table_name($this->table, $ctrl);
        foreach($this->columns as $cl){
            $ctrl::db_rm_column($tb, $cl);
            if ($cl->clIsIndex){
                $ctrl->db_drop_index($tb, $cl->clName);
            }
        }
    }
}