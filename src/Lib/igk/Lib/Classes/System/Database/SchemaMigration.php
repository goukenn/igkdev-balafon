<?php

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
     * migrate the definition
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


        $entries = $n->getElementsByTagName(DbSchemas::ENTRIES_TAG);
        $tentries = [];
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
            $tb = $v["TableName"];
            if ($resolvname)
                $tb = IGKSysUtil::DBGetTableName($v["TableName"], $ctrl);
            foreach ($v->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                $c[$cl->clName] = $cl;
            }
            $tables[$tb] = array(
                "ColumnInfo" => $c,
                "Controller" => $ctrl,
                "Description" => igk_getv($v, "Description"),
                "Entries" => igk_getv(
                    $tentries,
                    $tb
                )
            );
        }
        if ($resolvname && ($nmigrations = igk_getv($n->getElementsByTagName(DbSchemas::MIGRATIONS_TAG), 0))) {

            foreach ($nmigrations->getElementsByTagName(DbSchemas::MIGRATION_TAG) as $mig) {
                $v_m = new \IGK\System\Database\SchemaBuilderMigration();
                $v_m->controller = $ctrl;
                foreach ($mig->getChilds() as $c) {
                    if ($c instanceof \IGK\System\Html\Dom\HtmlCommentNode)
                        continue;
                    $fc = $c->tagName;
                    $item = $v_m->$fc()->load($c);
                    switch (strtolower($fc)) {
                        case "addcolumn":
                            $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                            $tabcl = &$tables[$tb]["ColumnInfo"];
                            foreach ($c->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                                $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                                $tabcl[$cl->clName] = $cl;
                            }
                            break;
                        case "removecolumn":
                            $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                            $tabcl = &$tables[$tb]["ColumnInfo"];
                            $item->columnInfo = $tabcl[$item->column];
                            unset($tabcl[$item->column]);
                            break;
                        case "changecolumn":
                            $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                            $tabcl = &$tables[$tb]["ColumnInfo"];
                            $item->columnInfo = $tabcl[$item->column];
                            foreach ($c->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                                $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                                $tabcl[$item->column] = $cl;
                            }
                            break;
                        case "renamecolumn":
                            $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                            $tabcl = &$tables[$tb]["ColumnInfo"];
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
        $relations = [];
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
