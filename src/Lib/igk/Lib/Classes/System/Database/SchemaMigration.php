<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaMigration.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;

use IDbGetTableReferenceHandler;
use IGK\Controllers\BaseController;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbRelation;
use IGK\Database\DbSchemas;
use IGK\System\Html\Dom\DomNodeBase;
use IGK\System\Html\XML\XmlNode;
use IGKSysUtil;
use IGK\Database\DbColumnInfoPropertyConstants as DB;
use IGK\Database\DbColumnInfoPropertyConstants;
use IGK\Database\DbModuleReferenceTable;
use IGK\Database\DbSchemasConstants;
use IGK\Helper\Activator;
use IGK\Helper\JSon;
use IGK\System\Console\Logger;
use IGKEvents;
use IGKModuleListMigration;

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
     * resolve name
     * @var ?string
     */
    var $resolvname;

    /**
     * default operation
     * @var mixed
     */
    var $operation;

    /**
     * migrate and load schema definition base on controller
     * @param mixed $ctrl 
     * @return array 
     */
    public function load($ctrl)
    {
        $reload = $this->reload;
        $n = $this->node;
        $resolvname = $this->resolvname;
        $version = $this->node['version'];
        $author = $n['author'];
        $date = $n['date'];

        $tables = &$this->table;
        $tbrelations = &$this->tbrelations;
        $migrations = &$this->migrations;
        $tentries = [];
        $relations = [];
        $links = [];
        if ($ctrl instanceof IDbGetTableReferenceHandler) {
            // table must merge with global system table            
            $gtables = $ctrl->getDataTablesReference($tables);
            $tables = &$gtables;
        }
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
            $constant = $v['clConstant'];
            if (empty($tb)) {
                continue;
            }
            if ($resolvname)
                $tb = IGKSysUtil::DBGetTableName($stb, $ctrl);
            foreach ($v->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                $c[$cl->clName] = $cl;

                // + | --------------------------------------------------------------------
                // + | Load links
                // + |
                $lnk = $vv['clLinkType'];
                if ($lnk) {
                    if (!isset($links[$lnk])) {
                        $links[$lnk] = [];
                    }
                    $links[$lnk][$tb][] = $cl->clName;
                }
            }
            $passing = null;
            foreach ($v->getElementsByTagName(IGK_GEN_COLUMS) as $vv) {
                $name = $vv["name"];
                $prefix = $vv["prefix"];
                if (!empty($name)) {
                    if (method_exists(self::class, $fc = "_Gen_" . $name)) {
                        if ($passing === null) {
                            $passing = (object)["columns" => &$c];
                        }
                        call_user_func_array([self::class, $fc], [$passing, $prefix]);
                    }
                }
            }
            $info = new SchemaMigrationInfo;
            $info->defTableName = $stb;
            $info->columnInfo = $c;
            $info->controller = $ctrl;
            $info->tableName = $tb;
            $info->description = igk_getv($v,  DbColumnInfoPropertyConstants::Description);
            $info->entries = igk_getv(
                $tentries,
                $tb
            );
            $info->modelClass = IGKSysUtil::GetModelTypeName($stb, $ctrl);
            $info->constant = $constant ? igk_bool_val($constant) : null;
            $tables[$tb] =  $info;
        }
        if (
            in_array($this->operation, [
                DbSchemasConstants::Downgrade,
                DbSchemasConstants::Migrate
            ]) &&
            ($resolvname && ($nmigrations = igk_getv($n->getElementsByTagName(DbSchemas::MIGRATIONS_TAG), 0)))
        ) {
            $v_mlist = $nmigrations->getElementsByTagName(DbSchemas::MIGRATION_TAG);
            switch ($this->operation) {
                case DbSchemasConstants::Downgrade:
                    $v_mlist && $this->downgrade($v_mlist, $tables, $ctrl);
                    break;
                default:
                    $v_mlist && $this->upgrade($v_mlist, $tables, $ctrl);
                    break;
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
        if ($tables instanceof DbModuleReferenceTable) {
            if ($this->table) {
                igk_dev_wln_e(__FILE__ . ":" . __LINE__,  "table defined");
            }
            // + | change tables to updated data
            $tables = $tables->udpate();
        }
        //+ | schema result response
        $v_result = compact(
            "tables",
            "tbrelations",
            "migrations",
            "relations",
            "links",
            // info that used - to create 
            "version",
            "author",
            "date"
        );
        return $v_result;
    }
    /**
     * use in visitor to update time column reference
     * @param object $clinfo 
     * @param null|string $prefix 
     * @return void 
     */
    private function _Gen_updateTime(object $clinfo, ?string $prefix = null)
    {
        $n = $prefix . "Create_At";
        $clinfo->columns[$n] = new DbColumnInfo([
            "clName" => $n, "clType" => "clDateTime", "clInsertFunction" => "Now()",
            "clNotNull" => "1", "clDefault" => "Now()"
        ]);
        $n = $prefix . "Update_At";
        $clinfo->columns[$n] = new DbColumnInfo(
            [
                "clName" => $n, "clType" => "clDateTime", "clInsertFunction" => "Now()",
                "clUpdateFunction" => "Now()", "clNotNull" => "1", "clDefault" => "Now()"
            ]
        );
    }

    /**
     * Load schema and migrate
     * @param DomNodeBase $node schema node 
     * @param mixed $result table response
     * @param null|array $tables 
     * @param mixed $tbrelations 
     * @param mixed $migrations 
     * @param mixed $ctrl 
     * @param bool $resolvname 
     * @param bool $reload 
     * @return static 
     */
    public static function LoadSchema(
        DomNodeBase $node,
        &$result,
        ?array &$tables = null,
        &$tbrelations = null,
        &$migrations = null,
        $ctrl = null,
        $resolvname = true,
        $reload = false,
        $operation = DbSchemasConstants::Migrate
    ) {
        $mi = new static;
        $mi->node = $node;
        $mi->table = &$tables;
        $mi->tbrelations = &$tbrelations;
        $mi->migrations = &$migrations;
        $mi->resolvname = $resolvname;
        $mi->reload = $reload;
        $mi->operation = $operation;
        $result = $mi->load($ctrl);
        return $mi;
    }
    private static function _DoUpgrade($key, $item, &$tables, $c, BaseController $ctrl)
    {

        switch ($key) {
            case DbSchemasConstants::OP_ADD_COLUMN:
                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                if (is_null($tables[$tb])) {
                    igk_wln_e("try to get migration " . $tb);
                }
                $tabcl = &$tables[$tb]->columnInfo;
                $after = $item->after;
                $keys = null;
                foreach ($c->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                    $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                    if ($after) {
                        $keys[$cl->clName] = $cl;
                    } else {
                        $tabcl[$cl->clName] = $cl;
                    }
                }
                if ($keys) {
                    $index = array_search($after, array_keys($tabcl));
                    $ckey = array_merge(array_slice($tabcl, 0, $index + 1), $keys, array_slice($tabcl, $index));
                    $tabcl = $ckey;
                }
                break;
            case DbSchemasConstants::OP_RM_COLUMN:

                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                $tabcl = &$tables[$tb]->columnInfo;
                $item->columnInfo = $tabcl[$item->column];
                unset($tabcl[$item->column]);
                break;
            case DbSchemasConstants::OP_CHANGE_COLUMN:
                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                $cl = $item->column;
                if (empty($cl)) {
                    igk_dev_wln_e("column not defined");
                    return;
                }
                if (empty($tb)) {
                    igk_dev_wln_e("table not defined ");
                    return;
                }
                $tabcl = &$tables[$tb]->columnInfo;
                if (!isset($tabcl[$cl])) {
                    igk_dev_wln_e("missing table column ", $cl);
                    return;
                }
                $item->columnInfo = $tabcl[$cl];
                foreach ($c->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                    $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                    igk_array_replace_key($tabcl, $item->column, $cl->clName, $cl);
                }
                break;
            case DbSchemasConstants::OP_RENAME_COLUMN:
                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                if (!isset($tables[$tb])) {
                    /// TODO: CHECK WHY column info not renderer 
                    // $tables[$tb] = (object)[
                    //     'columnInfo'=>[]
                    // ];
                    break;
                }
                $tabcl = &$tables[$tb]->columnInfo;
                if ($tabcl) {
                    $column = $tabcl[$item->column];
                    $column->clName = $item->new_name;

                    // + | --------------------------------------------
                    // + | rename a single key by replacing the old one ($arr, $old_key, $new_new, $new_value)
                    // + | 
                    $keys = array_keys($tabcl);
                    $pos = array_search($item->column, $keys);
                    $keys[$pos] = $item->new_name;
                    $vtabcl = array_slice($tabcl, 0, $pos);
                    $vtabcl[] = $column;
                    $vtabcl += array_slice($tabcl, $pos + 1);
                    $vtabcl = array_combine($keys, array_values($vtabcl));
                    $tabcl = $vtabcl;
                    igk_hook(IGKEvents::HOOK_DB_RENAME_COLUMN, ['table'=>$tb, 
                    'new_name'=>$item->new_name, 'column'=>$item->column]);
                } 
                break;
            case DbSchemasConstants::OP_CREATE_TABLE:
                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                $p = Activator::CreateNewInstance(SchemaMigrationInfo::class, [
                    DB::ColumnInfo => $item->columns,
                    DB::DefTableName => $item->table,
                    strtolower(DB::Description) => $item->description,
                    'tableName' => $tb,
                    '::context_db' => 'from create table - SchemaMigration',
                    'controller' => $ctrl,
                    'definitionResolver' => null,
                ]);
                if (!isset($tables[$tb])) {
                    $tables[$tb] = $p;
                } else {
                    igk_dev_wln_e("detect table already created ", __FILE__ . ":" . __LINE__);
                }
                break;
            case DbSchemasConstants::OP_DROP_TABLE:
                // $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                // $tables[$tb] = (object)[ 
                //     DB::DefTableName =>$tb, 
                // ];
                break;
            case DbSchemasConstants::OP_RENAME_TABLE:
                $tb = IGKSysUtil::DBGetTableName($item->to, $ctrl);
                $to = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                $p = igk_getv($tables, $to);
                if ($p) {
                    $p->defTableName = $item->to;
                    $p->tableName = $tb;
                    $p->modelClass = null;
                    igk_array_replace_key($tables, $to, $tb, $p);
                }
                break;
        }
    }
    private static function _DoDowngrade($key, $item, &$tables, $c, BaseController $ctrl)
    {
        switch ($key) {
            case DbSchemasConstants::OP_RENAME_TABLE:
                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                $to = IGKSysUtil::DBGetTableName($item->to, $ctrl);
                $p = igk_getv($tables, $to);
                if ($p) {
                    $p->defTableName = $item->table;
                    $p->tableName = $to;
                    $p->modelClass = null;
                    igk_array_replace_key($tables, $to, $tb, $p);
                }
                break;
            case DbSchemasConstants::OP_RM_COLUMN:
                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                $tabcl = &$tables[$tb]->columnInfo;
                $after = $item->after;
                $keys = null;
                foreach ($c->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                    $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                    if ($after) {
                        $keys[$cl->clName] = $cl;
                    } else {
                        $tabcl[$cl->clName] = $cl;
                    }
                }
                if ($keys) {
                    $index = array_search($after, array_keys($tabcl));
                    $ckey = array_merge(array_slice($tabcl, 0, $index + 1), $keys, array_slice($tabcl, $index));
                    $tabcl = $ckey;
                }
                break;
            case DbSchemasConstants::OP_ADD_COLUMN:
                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                $tabcl = &$tables[$tb]->columnInfo;
                $columns = $item->columns;
                // $item->columnInfo = $tabcl[$item->column];
                foreach ($c->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                    $name = $vv['clName'];
                    if ($name)
                        unset($tabcl[$name]);
                }
                break;
            case DbSchemasConstants::OP_CHANGE_COLUMN:
                if (empty($item->column)) {
                    igk_ilog('changecolumn, $item->column is empty');
                } else {
                    $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                    $tabcl = &$tables[$tb]->columnInfo;
                    // $item->columnInfo = $tabcl[$item->column];
                    //foreach ($c->getElementsByTagName(IGK_COLUMN_TAGNAME) as $vv) {
                    // $cl = DbColumnInfo::CreateWithRelation(igk_to_array($vv->Attributes), $tb, $ctrl, $tbrelations);
                    $tabcl[$item->column] = $item->columnInfo; //$cl;
                    //}
                }
                break;
            case DbSchemasConstants::OP_RENAME_COLUMN:
                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                $tabcl = &$tables[$tb]->columnInfo;
                // $column = $tabcl[$item->column];
                // $column->clName = $item->new_name;
                // $tabcl[$column->clName] = $column;
                $column = $tabcl[$item->new_name];
                $column->clName = $item->column;
                $tabcl[$column->clName] = $column;
                unset($tabcl[$item->new_name]);
                break;
            case DbSchemasConstants::OP_CREATE_TABLE:
                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                if (!isset($tables[$tb])) {
                    unset($tables[$tb]); // = $p;
                }

                break;
            case DbSchemasConstants::OP_DROP_TABLE:
                Logger::danger('restore data table is missing');
                // $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                //  $p = (object)[
                //     DB::ColumnInfo =>$item->columns,
                //     DB::DefTableName =>$item->table,
                //     strtolower(DB::Description) => $item->description
                // ];
                // if (!isset($tables[$tb])){
                //     $tables[$tb] = $p;
                // }else{
                //     igk_dev_wln_e("detect table already created ", __FILE__.":".__LINE__, );
                // }
                break;
        }
    }
    private function _do_migration($tmigrations, &$tables, BaseController $ctrl, $callback)
    {
        $migrations = &$this->migrations;
        $mighandler = new SchemaMigrationHookHandler;
        $mighandler->controller =  $ctrl;
        $mighandler->tables =  &$tables;
        $mighandler->register();
        foreach ($tmigrations as $mig) {
            $v_m = new \IGK\System\Database\SchemaBuilderMigration();
            $v_m->controller = $ctrl;
            foreach ($mig->getChilds() as $c) {
                if (empty($fc = $c->tagName) || ($c instanceof \IGK\System\Html\Dom\HtmlCommentNode))
                    continue;
                $item = $v_m->$fc()->load($c);
                $key = strtolower($fc);
                $callback($key, $item, $tables, $c, $ctrl);
            }
            $migrations[] = $v_m;
            if ($v_m->controller instanceof IGKModuleListMigration) {
                $v_m->controller = $v_m->controller->getHost();
                // igk_wln_e("list migration ...") ;
            }
        }
        $mighandler->unregister();
    }
    /**
     * oad schema and downgrade
     * @param mixed $migrations 
     * @param mixed $tables 
     * @param BaseController $ctrl 
     * @return void 
     */
    public function upgrade($migrations, &$tables,  BaseController $ctrl)
    {
        return $this->_do_migration($migrations, $tables, $ctrl, [self::class, '_DoUpgrade']);
    }
    /**
     * load schema and downgrade
     * @return void 
     */
    public function downgrade($migrations, $tables,  BaseController $ctrl)
    {
        return $this->_do_migration($migrations, $tables, $ctrl, [self::class, '_DoDowngrade']);
    }
}
