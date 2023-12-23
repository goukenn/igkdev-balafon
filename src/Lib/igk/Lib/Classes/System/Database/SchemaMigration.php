<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaMigration.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;

use IDbGetTableReferenceHandler;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
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
use IGK\Helper\IO;
use IGK\Helper\JSon;
use IGK\System\Caches\DBCaches;
use IGK\System\Console\Logger;
use IGK\System\Database\Traits\SchemaGenerationFieldTrait;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\HtmlReader;
use IGK\System\IO\Path;
use IGKEvents;
use IGKException;
use IGKModuleListMigration;
use ReflectionException;

/**
 * migration handler
 * @package IGK\System\Database
 */
class SchemaMigration
{
    use SchemaGenerationFieldTrait;
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
        // + | --------------------------------------------------------------------
        // + | LOAD DB-SCHEMA DEFINITION 
        // + |

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

        $qtb = [$n];
        $v_loadschema = [];
        $v_roots = [];
        $v_tprefix = null;
        while (count($qtb) > 0) {
            // looping thru node 
            $n = array_shift($qtb);
            if (!$n) continue;
            if ($v_roots && ($v_roots[0] === $n)) {
                //
                array_pop($v_roots);
            } else {
                if ($requires = $n->getElementsByTagName(DbSchemas::RT_REQUIRESCHEMA_TAG)) {
                    $ts = [];
                    foreach ($requires as $rq) {
                        self::_LoadRequireSchema($ts, $rq, $v_loadschema);
                    }
                    if ($ts) {
                        $qtb += $ts;
                        $v_roots[] = $n;
                        array_push($qtb, $n);
                        continue;
                    } else {
                        // igk_wln_e("load missing...");
                    }
                }
            }

            if ($ctrl instanceof IDbGetTableReferenceHandler) {
                // table must merge with global system table            
                $gtables = $ctrl->getDataTablesReference($tables);
                //$tables = &$gtables;
                $tables = &$gtables->getRefTableDefinition();

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
                $prefix = $v['Prefix'];
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
                // + | load generate columns
                /// TODO: GEN COLUM for migration
                $passing = null;
                foreach ($v->getElementsByTagName(IGK_GEN_COLUMS) as $vv) {
                  
                    self::UpdateGenColumn($vv, $c, $passing);                   
                }
                // + | load table constraint schema            
                $fconstraints = null;
                foreach ($v->getElementsByTagName(IGK_FOREIGN_CONSTRAINT) as $vv) {
                    if (is_null($fconstraints)) {
                        $fconstraints = [];
                    }
                    $on = $vv["on"];
                    $from = $vv["from"];
                    $columns = $vv["columns"];
                    $foreignKeyName = $vv["foreignKeyName"];

                    $fconstraints = Activator::CreateNewInstance(SchemaForeignConstraintInfo::class, compact('on', 'from', 'columns', 'foreignKeyName'));
                }

                $v_tprefix = igk_getv($v, 'prefix');
                $info = new SchemaMigrationInfo;
                $info->defTableName = $stb;
                $info->columnInfo = $c;
                $info->controller = $ctrl;
                $info->tableName = $tb;
                $info->prefix = $v_tprefix;
                $info->description = igk_getv($v,  DbColumnInfoPropertyConstants::Description);
                $info->entries = igk_getv(
                    $tentries,
                    $tb
                );
                $info->modelClass = IGKSysUtil::GetModelTypeName($stb, $ctrl);
                $info->constant = $constant ? igk_bool_val($constant) : null;
                $info->foreignConstraint = $fconstraints;
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
                        if (!empty($tables))
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
    public static function UpdateGenColumn($node, & $cl, $passing=null){
        $name = $node["name"];
        $prefix = $node["prefix"];
        if (!empty($name)) {
            if (method_exists(self::class, $fc = "_Gen_" . $name)) {
                if ($passing === null) {
                    $passing = (object)["columns" => &$cl];
                }
                call_user_func_array([self::class, $fc], [$passing, $prefix]);
            }
        }
    }
    /**
     * load require schema 
     * @param array $tab 
     * @param mixed $rq 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _LoadRequireSchema(array &$tab, $rq, &$load_schema)
    {
        extract((array)igk_createobj_filter($rq->getAttributes()->to_array(), "from|name|argument|file"));

        switch ($from) {
            case 'self':
                list($file, $p) = $rq->getInheritedParam('migration:info') ?? [null, null];

                if ($file) {
                    self::_loadControllerRequireSchema($p, $tab, $argument, $load_schema);
                }
                break;
            case 'module':
                // load module definition 
                if ($p = igk_require_module($name, null, 1, 0)) {

                    self::_loadControllerRequireSchema($p, $tab, $argument, $load_schema);
                }
                break;
            case 'controller':
            case 'project':
                if ($p = $name ? igk_getctrl($name) : null) {
                    self::_loadControllerRequireSchema($p, $tab, $argument, $load_schema);
                }
                break;
        }
    }
    private static function _loadControllerRequireSchema(?BaseController $p, &$tab, $argument, &$load_schema)
    {
        if (is_null($argument)) {
            $files = [Path::Combine($p->getDataDir(), 'data.schema.xml')];
        } else {
            if ($argument == "*") {
                // load all data.schema
                $files = IO::GetFiles($p->getDataDir(), "/\.db-schema.xml$/", false);
            } else
                $files = [$argument];
        }
        while (count($files) > 0) {
            $f = $argument = array_shift($files);
            if (!$f) continue;

            if (
                file_exists($f) ||
                ($p  && (file_exists($f = $p->getDataDir() . "/" . $argument . ".db-schema.xml") ||
                    file_exists($f = $p->getDataSchemaFile($argument))
                )
                )
            ) {
                if (isset($load_schema[$f])) {
                    continue;
                }
                if ($n = HtmlReader::LoadFile($f)) {
                    if ($c = igk_getv($n->getElementsByTagName(DbSchemas::RT_SCHEMA_TAG), 0)) {
                        $c->setParam('migration:info', [$f, $p]);
                        array_push($tab, $c);
                    }
                }
                $load_schema[$f] = 1;
            }
        }
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
    /**
     * resolve db cache information 
     * @param array $tables 
     * @param string $tb 
     * @return bool 
     * @throws IGKException 
     */
    private static function _ResolvDbCacheDefinition(array &$tables, string $tb):bool
    {
        // - get external table information 
        if ($tbinfo = DBCaches::GetTableInfo($tb, null)) {
            $tables[$tb] = $tbinfo;
            if (!$tbinfo->modelClass) {
                $tbinfo->modelClass = IGKSysUtil::GetModelTypeName($tbinfo->defTableName, $tbinfo->controller);
            }
            return true;
        } else {
            igk_dev_wln_e("try to get migration " . $tb);
        }
        return false;
    }
    private static function _DoUpgrade($key, $item, array &$tables, $c, ?BaseController $ctrl)
    {

        switch ($key) {
            case DbSchemasConstants::OP_ADD_COLUMN:
                $tb = IGKSysUtil::DBGetTableName($item->table, $ctrl);
                if (!isset($tables[$tb])) {
                    if (!self::_ResolvDbCacheDefinition($tables, $tb)) {
                        return;
                    }
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
                if (key_exists($tb, $tables)){
                    $tabcl = &$tables[$tb]->columnInfo;
                    $item->columnInfo = $tabcl[$item->column];
                    unset($tabcl[$item->column]);
                }
                else {
                    $inf = DbSchemas::GetTableColumnInfo($tb);
                    if ($inf){
                        $tables[$tb] = & $inf;
                        if (isset($inf[$item->column])){
                            unset($inf[$item->column]);
                        }
                        break;
                    }

                    igk_dev_wln_e(__FILE__.":".__LINE__ , "table not present is schema tables definition");
                }
                break;
            case DbSchemasConstants::OP_CHANGE_COLUMN:
                $item->table || igk_die("migration: change column missing table name");
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
                    igk_dev_wln_e("[schemap-migration] missing table column ", $cl);
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
                    igk_hook(IGKEvents::HOOK_DB_RENAME_COLUMN, [
                        'table' => $tb,
                        'new_name' => $item->new_name, 'column' => $item->column
                    ]);
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
                $ref = igk_getv($tables, $tb);
                if (is_null($ref)){
                    igk_wln_e("is null");
                }
                $tabcl = & $tables[$tb]->columnInfo;
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
    private function _do_migration($tmigrations, array &$tables, ?BaseController $ctrl, $callback)
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
    public function upgrade($migrations, array &$tables,  ?BaseController $ctrl)
    {
        return $this->_do_migration($migrations, $tables, $ctrl, [self::class, '_DoUpgrade']);
    }
    /**
     * load schema and downgrade
     * @return void 
     */
    public function downgrade($migrations, array &$tables,  BaseController $ctrl)
    {
        return $this->_do_migration($migrations, $tables, $ctrl, [self::class, '_DoDowngrade']);
    }
}
