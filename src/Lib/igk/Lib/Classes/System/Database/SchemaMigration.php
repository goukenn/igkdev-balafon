<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaMigration.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;

use IGK\Database\DbColumnInfo;
use IGK\Database\DbRelation;
use IGK\Database\DbSchemas;
use IGK\System\Html\XML\XmlNode;
use IGKSysUtil;

/**
 * migration handler
 * @package IGK\System\Database
 */
class SchemaMigration
{
    var $node;

    var $reload;

    var $resovlname;

    var $table;

    var $tbrelations;

    var $migrations;
 
    /**
     * migrate and load schema definition 
     * @param mixed $ctrl 
     * @return array 
     */
    public function migrate($ctrl)
    {
      
        $reload = $this->reload ;
        $n = $this->node;  
        $resolvname = $this->resolvname;    
        
        $tables = & $this->table;
        $tbrelations = & $this->tbrelation ;
        $migrations = & $this->migrations ;
        $tentries = [];
        $relations = [];


        $entries = $n->getElementsByTagName(DbSchemas::ENTRIES_TAG);

     
        if ($entries) {
            while ($c_entries = array_shift($entries)) {
                foreach ($c_entries->getElementsByTagName(DbSchemas::ROWS_TAG) as $v) {
                    if ($tb = $v["For"]) {
                        $tb = $resolvname ? IGKSysUtil::DBGetTableName($tb, $ctrl) : $tb;
                        $rtab = [];
                        foreach ($v->getElementsByTagName("Row") as $item) {
                            if ($attr = $item->getAttributes()) {
                                array_push($rtab, $attr->to_array());
                            }
                        }
                        if (isset($tentries[$tb])) {
                            $tentries[$tb] = array_merge($tentries[$tb], $rtab);
                        } else {
                            $tentries[$tb] = $rtab;
                        }
                    }
                }
            }
        }
        foreach ($n->getElementsByTagName(DbSchemas::DATA_DEFINITION) as $v) {
            $c = array();
            $tb = $stb = $v["TableName"];
            if ($resolvname)
                $tb = IGKSysUtil::DBGetTableName($stb, $ctrl);
            foreach ($v->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                $c[$cl->clName] = $cl;
            }
            $passing = null;
            foreach($v->getElementsByTagName(IGK_GEN_COLUMS) as $vv){
                $name = $vv["name"];
                $prefix = $vv["prefix"];
                if (!empty($name)){
                    if (method_exists(self::class, $fc = "_Gen_".$name)){
                        if ($passing === null){
                            $passing = (object)["columns"=>& $c];   
                        }
                        call_user_func_array([self::class, $fc], [$passing, $prefix]);
                    } 
                } 
            }  
            $info = new SchemaMigrationInfo;
            $info->defTableName=$stb;
            $info->columnInfo = $c;
            $info->controller = $ctrl;
            $info->description = igk_getv($v, "Description");
            $info->entries = igk_getv(
                $tentries,
                $tb
            );
            $info->modelClass = IGKSysUtil::GetModelTypeName($stb, $ctrl );
            $tables[$tb] =  $info;
        }
        
        if ($resolvname && ($nmigrations = igk_getv($n->getElementsByTagName(DbSchemas::MIGRATIONS_TAG), 0))) {

            foreach ($nmigrations->getElementsByTagName(DbSchemas::MIGRATION_TAG) as $mig) {
                $v_m = new \IGK\System\Database\SchemaBuilderMigration();
                $v_m->controller = $ctrl;
                foreach ($mig->getChilds() as $c) {
                    if (empty($fc = $c->tagName) || ($c instanceof \IGK\System\Html\Dom\HtmlCommentNode))
                        continue;
                    $item = $v_m->$fc()->load($c);
                    switch (strtolower($fc)) {
                        case "addcolumn":
                            $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                            $tabcl = &$tables[$tb]->columnInfo;
                            foreach ($c->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                                $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                                $tabcl[$cl->clName] = $cl;
                            }
                            break;
                        case "removecolumn":
                            $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                            $tabcl = &$tables[$tb]->columnInfo;
                            $item->columnInfo = $tabcl[$item->column];
                            unset($tabcl[$item->column]);
                            break;
                        case "changecolumn":
                            $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                            $tabcl = &$tables[$tb]->columnInfo;
                            $item->columnInfo = $tabcl[$item->column];
                            foreach ($c->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                                $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                                $tabcl[$item->column] = $cl;
                            }
                            break;
                        case "renamecolumn":
                            $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                            $tabcl = &$tables[$tb]->columnInfo;
                            $column = $tabcl[$item->column];
                            $column->clName = $item->new_name;
                            $tabcl[$column->clName] = $column;
                            unset($tabcl[$item->name]);
                            break;
                            // case "altercolumn":
                            // use change column definition
                            //    break;
                    }
                }
                $migrations[] = $v_m;
            }
        }
      
        if ($v_t_relation = igk_getv($n->getElementsByTagName(DbSchemas::RELATIONS_TAG), 0)) {
            foreach ($v_t_relation->getElementsByTagName(DbSchemas::RELATION_TAG) as $vv) {
                $cl = DbRelation::Create(igk_to_array($vv->Attributes), $ctrl);
                if ($cl) {
                    $relations[$cl->name] = $cl;
                }
            }
        }
        $v_result = compact("tables", "tbrelations", "migrations", "relations");
        return $v_result;
    }
    /**
     * use in visitor to update time column reference
     * @param object $clinfo 
     * @param null|string $prefix 
     * @return void 
     */
    private function _Gen_updateTime(object $clinfo, ?string $prefix=null){
        $n = $prefix."create_at";
        $clinfo->columns[$prefix."create_at"]= new DbColumnInfo([
            "clName"=>$n, "clType"=>"clDateTime", "clInsertFunction"=>"Now()",
            "clNotNull"=>"1", "clDefault"=>"Now()"
        ]);
        $clinfo->columns[$prefix."update_at"]= new DbColumnInfo(
            [
                "clName"=>$n, "clType"=>"clDateTime", "clInsertFunction"=>"Now()",
                "clUpdateFunction"=>"Now()", "clNotNull"=>"1", "clDefault"=>"Now()"
            ]
        ); 
    }

    /**
     * 
     * @param XmlNode $node schema node 
     * @param mixed $result table response
     * @param null|array $tables 
     * @param mixed $tbrelations 
     * @param mixed $migrations 
     * @param mixed $ctrl 
     * @param bool $resolvname 
     * @param bool $reload 
     * @return static 
     */
    public static function LoadSchema (XmlNode $node, & $result, ?array & $tables=null,  &$tbrelations = null, &$migrations = null, $ctrl = null, $resolvname = true, $reload = false ){
        $mi = new static;
        $mi->node = $node;
        $mi->table = &$tables;
        $mi->tbrelations = &$tbrelations;
        $mi->migrations = &$migrations;
        $mi->resolvname = $resolvname;
        $mi->reload = $reload;
        $result = $mi->migrate($ctrl);
        return $mi;
    }
}
