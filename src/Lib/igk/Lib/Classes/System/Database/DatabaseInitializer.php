<?php
// @author: C.A.D. BONDJE DOUE
// @file: DatabaseInitializer.php
// @date: 20221118 21:40:33
namespace IGK\System\Database;

use IDbGetTableReferenceHandler;
use IGK\Controllers\ApplicationModuleController;
use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerExtension;
use IGK\Controllers\SysDbController;
use IGK\Database\DbSchemas;
use IGK\Database\DbSchemasConstants;
use IGK\Helper\Database;
use IGK\Helper\Project;
use IGK\System\Caches\DBCaches;
use IGK\System\Console\Logger;
use IGK\System\Controllers\ApplicationModules;
use IGK\System\Database\Traits\DbCreateTableReferenceTrait;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Database\IDbResolveLinkListener;
use IGKEvents;
use IGKException;
use IGKModuleListMigration;
use IGKType;
use ReflectionException;
use ReflectionMethod;

///<summary></summary>
/**
 * 
 * @package IGK\System\Database
 */
class DatabaseInitializer implements IDbGetTableReferenceHandler, IDbResolveLinkListener
{
    use DbCreateTableReferenceTrait;
    private $m_hostController;
    private $m_defs = [];
    private $m_parentController;
    private $m_controllers = [];
    /**
     * store.
     * @var mixed
     */
    private $m_resolvedLinks;

    /**
     * store. 
     * @var array
     */
    private $m_init_tables = [];
    /**
     * store tables definitions
     * @var array
     */
    var $tables = [];
    /**
     * store entries 
     * @var array
     */
    var $entries = [];
    /**
     * store migrations 
     * @var array
     */
    var $migrations = [];

    var $relations = [];

    var $definitions = [];

    /**
     * default resolutions
     * @var mixed
     */
    var $resolv;

    public function getDataTablesReference(&$table)
    {
    }
    /**
     * get loaded definition array
     * @return array 
     */
    public function getDefs()
    {
        return $this->m_defs;
    }
    public function __construct(?BaseController $ctrl = null)
    {
        $this->m_parentController = (!$ctrl ||
            ($ctrl instanceof ApplicationModuleController) ? null : $ctrl)
            ?? SysDbController::ctrl();
    }
    /**
     * init with this controller
     * @param BaseController $sysctrl controller base on the initialization
     * @param string $operation schema loading operation mode
     * @return object newly created definition - that will be the global bindign reference
     * @throws IGKException 
     */
    public function init(BaseController $sysctrl, string $op = DbSchemasConstants::Migrate)
    {
        $ad_name = $sysctrl->getDataAdapterName();
        $definition = null;
        // load system schema definition
        if ($sysctrl->getCanInitDb()) {
            $file = $sysctrl->getDataSchemaFile();
            if ($definition = DbSchemas::LoadSchema($file, $sysctrl, true, $op)) {
                $this->add($ad_name, $definition);
                $this->m_defs[$sysctrl->getName() . '/init']
                    = [$sysctrl,  (array)$definition, $definition->tables];
                $this->resolv = $ad_name;
                // unset($definition->tables);
                // $definition->tables = [];
            }
        }
        return $definition;
    }

    /**
     * add core definition
     * @param mixed $adaptername 
     * @param mixed $definition 
     * @return void 
     */
    public function add($adaptername, $definition)
    {
        $this->definitions[$adaptername] = $definition;
    }
    /**
     * get loaded definition form adapter 
     * @param mixed $adaptername 
     * @return mixed 
     * @throws IGKException 
     */
    public function get($adaptername)
    {
        return igk_getv($this->definitions, $adaptername);
    }
    /**
     * upgrade all table definition 
     * @param BaseController $controller 
     * @param array $definition schemas definition tables to upgrate
     * @param null|DatabaseInitializer $caches 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     */
    public function upgrade(BaseController $controller, array $definition, ?DatabaseInitializer $caches = null)
    {
        igk_hook(IGKEvents::HOOK_DB_INIT_START, ['initializer' => $this, 'method' => 'upgrade']);
        $g = false;
        if ($ad = $controller->getDataAdapter()){
            $g = $ad->connect();
        }

        $rs = $definition;
        $post_install = [];
        $count = 0;
        $current = $controller;
        $this->m_parentController = $controller;
        $plist = (object)['tables' => []];
        foreach ($rs  as $name => $tbinfo) {
            $count++;
            $ctrl = $tbinfo->controller;
            if ($ctrl instanceof ApplicationModuleController) {
                $post_install[] = [$ctrl, $tbinfo, $this->m_parentController];
                continue;
            }
            if ($current != $tbinfo->controller) {
                $this->_initLogic($current, $plist->tables);
                $plist->tables = [];
                $current = $tbinfo->controller;
            }
            $plist->tables[$name] = $tbinfo;
        }
        $this->_initLogic($current, $plist->tables);

        Logger::info('post install migration ... --- ');

        // foreach ($post_install as $v) {
        //     list($ctrl, $tbinfo) = $v;
        //     $src = $this->m_parentController;
        //     SchemaBuilderHelper::Migrate($tbinfo, $src);
        // }

        if ($caches) {
            array_map(function ($a) {
                //
                $ctrl = $a[0];
                // $ctrl->register_autoload(); 
                $ad = $ctrl->getDataAdapter();
     
                if ($ad && isset($a[1])) {
                    $info = (object)$a[1];
                    $this->m_resolvedLinks = (object)[
                        'resolved'=>[],
                        'tables'=>$info->tables,
                        'adapter'=>$ad,
                    ];                      
                    $ad->resolveLinkListener = $this;

                    foreach ($info->tables  as $table => $tbinfo) {
                        if (isset($this->m_resolvedLinks->resolved[$table])){
                            continue;
                        }
                        if ($tbinfo->entries) {
                            $this->_load_entries($ad, $table, $tbinfo->entries, $tbinfo->columnInfo);
                        }
                        $this->m_resolvedLinks->resolved[$table] = 1;
                        $this->m_init_tables[$table] = 1;
                    }
                    $ad->resolveLinkListener = null;
                }  
                return Database::InitDataEntries($a[0]);
            }, $caches->m_defs);
        }
        if($g) $ad->close();
        igk_hook(IGKEvents::HOOK_DB_INIT_COMPLETE, []);
    }
    /**
     * 
     * @param mixed $ad 
     * @param mixed $tableName 
     * @param mixed $entries 
     * @param mixed $columnInfo 
     * @return void 
     */
    private function _load_entries($ad, $tableName, $entries, $columnInfo){
        foreach ($entries as $row) {
            $rs = $ad->select($tableName, $row, null);
            if (!$rs && ($rs->getRowCount()==0)){
                $ad->insert($tableName, $row, $columnInfo);
            }
        }
    }
    public function resolve(string $linkTable): bool{
        if (isset($this->m_resolvedLinks->resolved[$linkTable])){
            return true;
        }
        // resolve 
        // $dependency = [];
        $ad = $this->m_resolvedLinks->adapter;
        if (isset($this->m_resolvedLinks->tables[$linkTable])){
           // first load resolved links tab  
           $g = $this->m_resolvedLinks->tables[$linkTable];
           if ($g->entries){
                $this->_load_entries($ad, $linkTable, $g->entries, $g->columnInfo);
           }
           $this->m_resolvedLinks->resolved[$linkTable] = 1;
           return true;
        } else {
            // + | depend on table that is not in controller scope
            if (isset($this->m_init_tables[$linkTable])){
                $this->m_resolvedLinks->resolved[$linkTable] = 1;
                return true;
            }
            igk_die("die : table not in scope - ".$linkTable);
        }
        return false;
    }
    private function _MigrateModuleCallback(BaseController $a, $info,  $parent)
    {
        if (!($a instanceof ApplicationModuleController))
            return null;
        SchemaBuilderHelper::Migrate($info, $parent);
    }
    private function _initLogic(BaseController $ctrl, $tables)
    {
        Logger::info('init ... ' . $ctrl);

        Database::CreateTableBase($ctrl, $tables, null);

        Logger::info('migrate ...');

        SchemaBuilderHelper::Migrate($tables);
        //-
        // init require model logic
        Database::InitDbCoreLogic($ctrl, $tables, true);
    }


    private $m_definition;
    /**
     * 
     * @param string $file 
     * @param mixed $definition 
     * @param BaseController|IDbTableReference $tableReferenceResolver 
     * @param string $operation 
     * @param self $operation 
     * @return void 
     * @throws IGKException 
     */
    public static function InitSchemaDefinition(
        string $file,
        $definition,
        $tableReferenceResolver,
        $operation = DbSchemasConstants::Migrate,
        DatabaseInitializer $initializer = null
    ) {
        //$this->m_definition = $definition;
        Logger::info('load - ' . $tableReferenceResolver);
        $def = DbSchemas::LoadSchema($file, $tableReferenceResolver, true, $operation);

        // update reference 
        if ($def) {
            $empty = true;
            foreach ($def as $k => $v) {
                $empty &= empty($v);
                if (is_array($v) && !$definition->$k) {
                    $definition->$k = [];
                }
                // if (is_string($v)){
                //     igk_wln_e("string , ", $v);
                // }
                if (is_array($definition->$k)){

                    $definition->$k = array_merge(
                        array_values($definition->$k ?? []),
                        array_values($v ?? [])
                    );
                }else{
                    $definition->$k = $v;
                }
            }
            if ($initializer && !$empty) {
                $c = $initializer->m_hostController;
                if ($c) {
                    $initializer
                        ->m_defs[$c->getName() . '/load-schema']
                        = [$c, (object)(array)$def];
                    //$def->controller = $c;
                }
            }
        }
    }
    public function loadSchemaDefinition(
        string $file,
        $refTableResolver = null,
        $operation = DbSchemasConstants::Migrate,
        ?BaseController $controller = null
    ) {
        $this->m_hostController = $controller ?? $refTableResolver;
        $def = $this->get($this->resolv);
        self::InitSchemaDefinition(
            $file,
            $def,
            $refTableResolver,
            $operation,
            $this
        );
        $this->m_hostController = null;
    }
    public function resolvTableDefinition(string $table)
    {
        return igk_getv($this->m_definition[$this->resolv], $table);
    }

    public function loadSystemProjects(string $op = DbSchemasConstants::Migrate)
    {
        $projects = Project::GetProjectInvocatorInitDbList(SysDbController::ctrl());
        foreach ($projects->getItems() as $p) {
            if (is_file($file = $p->getDataSchemaFile())) {
                $this->loadSchemaDefinition(
                    $file,
                    $p,
                    $op,
                    $p
                );
            }
        }
    }
    /**
     * load module migration
     * @param string $op 
     * @return void 
     * @throws IGKException 
     */
    public function loadSystemModules(string $op = DbSchemasConstants::Migrate)
    {
        if ($migrations = IGKModuleListMigration::CreateModulesMigration()) {
            $migrations->loadMigrationSchema($this, $op);
        }
    }
}
