<?php
// @author: C.A.D. BONDJE DOUE
// @file: DBCaches.php
// @date: 20221119 11:34:09
namespace IGK\System\Caches;
use Exception;
use IGK\Controllers\ApplicationModuleController;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbSchemas;
use IGK\Helper\Activator;
use IGK\Helper\Utility;
use IGK\System\Console\Logger;
use IGK\System\Controllers\ApplicationModules;
use IGK\System\Database\DatabaseInitializer;
use IGK\System\Database\DbUtils;
use IGK\System\Database\SchemaMigrationInfo;
use IGK\System\EntryClassResolution;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\NotImplementException;
use IGKEvents;
use IGKException;
use IGKSysUtil;
use ReflectionException;

/**
 * 
 * @package IGK\System\Caches
 */
/**
* auto generate doc.
* @package IGK\System\Caches
*/
class DBCaches
{
    /**
     * base name of schema definition to store 
     */
    const CACHE_FILE_NAME = '.data-schema.definition.cache';
    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;
    /**
    * Property: db initializer.
    * @var mixed
    */
    private $m_db_initializer;
    /**
    * Property: db init request.
    * @var mixed
    */
    private $m_db_init_request;
    /**
    * Property: mock.
    * @var mixed
    */
    private $m_mock;
    /**
     * store controller loaded definition
     * @var ?array|?object
     */
    private $m_db_defs;
    /**
     * return loaded controller info 
     * @param BaseController $controller 
     * @return <string, SchemaMigrationInfo>[]
     */
    public static function GetControllerDataTableDefinition(BaseController $controller){
        if (!self::getInstance()->m_init_cache){
            self::Init();
        }
        $mp = self::GetCacheData();
        /**
         * @var string $table 
         * @var SchemaMigrationInfo $info 
         */
        $table=null; 
        $list = [];
        foreach($mp as $table=>$info){
            if ($info->controller == $controller){
                $list[$table] = $info;
            }
        }
        return $list;
    }
    /**
     * init db request 
     * @return mixed 
     */
    public static function InitRequest()
    {
        return self::getInstance()->m_db_init_request;
    }
    /**
     * retrieve the full cache directory 
     * @return string 
     */
    public static function GetCacheFile()
    {
        $cache_dir = igk_io_cachedir() . DIRECTORY_SEPARATOR . self::CACHE_FILE_NAME;        
        return $cache_dir;
    }
    /**
     * retrieve cached data
     * @return array 
     */
    public static function GetCacheData()
    {
        return self::getInstance()->m_tableInfo;
    }
    /**
    * Returns Cache Initializer.
    */
    public static function GetCacheInitializer()
    {
        return self::getInstance()->m_db_initializer;
    }
    /**
    * auto generate doc.
    * @return mixed
    */
    public static function IsInitializing()
    {
        return self::getInstance()->m_initializing;
    }
    /**
     * table register according to system management database
     * @var array
     */
    private $m_tableInfo = [];
    /**
    * Cache: init cache.
    * @var mixed
    */
    private $m_init_cache = false;
    /**
    * Property: initializing.
    * @var mixed
    */
    private $m_initializing = false;
    /**
     * get the dbcache instances
     * @return static
     */
    private static function getInstance()
    {
        if (self::$sm_instance === null)
            self::$sm_instance = new static;
        return self::$sm_instance;
    }
    /**
     * init DBCaches - entry point
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public static function Init()
    {
        $i = self::getInstance();
        if ($i->m_init_cache) {
            return;
        }
        $i->_initDbCache();
    }
    /**
    * reset the db cache info
    * @param bool $force
    * @throws NotImplementException
    * @return never
    */
    public static function Reset(bool $force=false)
    {
        static::getInstance()->_clearAndReload($force);
    }
    /**
    * retrieve cached table column info -
    * @param string $table
    * @param null|BaseController $controller
    * @param mixed & $table_info
    * @throws IGKException
    * @return SchemaMigrationInfo|array
    */
    public static function GetColumnInfo(string $table, ?BaseController $controller = null, & $table_info = null)
    {
        return static::getInstance()->resolve($table, $controller, $table_info);
    }
    /**
    * get table information
    * @param string $table
    * @param ?BaseController $controller
    * @return ?SchemaMigrationInfo
    */
    public static function GetTableInfo(string $table, ?BaseController $controller = null)
    {
        $v_i = static::getInstance();
        if (!$v_i->m_init_cache || is_null($v_i->m_tableInfo)){
            self::GetColumnInfo($table, $controller);
        }
        $c = igk_getv($v_i->m_tableInfo, $table);
        if ($controller && $c) {
            // + | --------------------------------------------------------------------
            // + | check matching - controller - root module will be a sysdb model
            // + |
            if (!($controller instanceof SysDbController) && 
                ($controller != $c->controller)) {
                igk_die("db retrieve but controller not match");
            }
        }
        return $c;
    }
    /**
    * Returns.
    * @param string $n
    */
    public static function Get(string $n)
    {
        $g = static::getInstance();
        return igk_getv($g->m_tableInfo, $n);
    }
    /**
     * register table information 
     * @param string $table 
     * @param mixed $info 
     * @return void 
     */
    public static function Register(string $table,  $info)
    {
        $g = static::getInstance();
        $g->m_tableInfo[$table] = $info;
    }
    /**
     * helper: clear db caches
     * @return void 
     */
    public static function Clear()
    {
        static::getInstance()->_clear();
    }
    /**
    * .ctr
    * @return mixed
    */
    private function __construct()
    {
    }
    /**
    * get string presentation.
    */
    public function __toString()
    {
        return 'Systemp - DB Cache';
    }
    /**
     * clear stored db caches
     * @return void 
     */
    private function _clear()
    {
        $this->m_tableInfo = [];
        $this->m_init_cache = false;
        if (igk_io_file_exists($file = self::GetCacheFile(), true))
            @unlink($file);
        DbSchemas::ResetSchema();
    }
    /**
    * clear and restore store db cache
    * @param bool $force
    * @throws IGKException
    * @throws ArgumentTypeNotValidException
    * @throws ReflectionException
    * @throws Exception
    * @return void
    */
    private function _clearAndReload(bool $force=false)
    {
        $this->_clear();
        $this->_initDbCache($force);
    }
    /**
     * initialize schema cache
     * @param bool $force 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    protected function _initDbCache(bool $force=false)
    {
        if ($this->m_initializing) {
            return;
        }
        // + | -------------------------------------------------------------------------------------
        // + | cache is empty - load from cache -  convert stClass - close to migration DbColumnInfo
        // + |
        $sysctrl = SysDbController::ctrl();
        if (!$this->m_init_cache) {
            if (is_file($file = self::GetCacheFile())) {
                $data = unserialize(file_get_contents($file));
                if ($data !== false) {
                    $ad_name = $sysctrl->getDataAdapterName();
                    $trdata = igk_getv($data->{'0'}, $ad_name);
                    $rdata = [];
                    if ($trdata) {
                        foreach ($trdata as $ctrl => $v) {
                           
                            if (!($gctrl = igk_getctrl($ctrl, false))) {
                                continue;
                            }
                            foreach ($v as  $d) {
                                // + | --------------------------------------------------------------------
                                // + | load DB cache info
                                // + |  
                                $v_tbname = $d->tableName;
                                if (!isset($v_tbname)){
                                    igk_dev_wln_e(__FILE__.":".__LINE__ , 
                                    $file,                                     
                                    'missing definition on <pre>', json_encode($d, JSON_PRETTY_PRINT), '</pre>');
                                }else{                           
                                    $rdata[$v_tbname] = SchemaMigrationInfo::CreateFromCacheInfo($d, $gctrl);
                                }
                            }
                        }
                    } else {
                        igk_dev_wln_e(
                            __FILE__ . ":" . __LINE__,
                            "cache missing configuration",
                            $trdata
                        );
                    }
                    $this->m_tableInfo = $rdata;
                    $this->m_db_defs =  $trdata;
                    $this->m_init_cache = 1;
                    return;
                }
            }
        }
        $this->m_init_cache = 1;
        $this->m_initializing = 1;
        $this->m_db_init_request = 1;
        igk_environment()->NO_PROJECT_AUTOLOAD = 1;
        $db = new DatabaseInitializer;
        $definition = $db->init($sysctrl);
        $this->m_tableInfo = $definition->tables; 
        $this->m_db_initializer = $db;
        $db->loadSystemProjects();
        $db->loadSystemModules();
        foreach ($db->getDefs() as $p) {
            list($ctrl, $info) =  $p;
            if ($ctrl == $sysctrl)
                continue;
            foreach ($info->tables as $tablen => $info) {
                if (is_numeric($tablen)) {
                    $tablen = $info->tableName;
                }
                if (key_exists($tablen, $this->m_tableInfo)) {
                    if ($info->controller != $sysctrl ){
                        Logger::warn(sprintf('%s\'s table will enter in conflict with %s', $info->controller , $tablen));
                    }
                    continue;
                }
                $info->controller = $ctrl;
                $this->m_tableInfo[$tablen] =  $info;
            }
        }
        // + | --------------------------------------------------------------------
        // + | load to speed loading
        // + |
        self::CacheData($this->m_tableInfo);
        $this->m_initializing = false;
        $this->m_db_init_request = false;
        igk_environment()->NO_PROJECT_AUTOLOAD = null;
        // + | --------------------------------------------------------------------
        // + | check and init data model 
        // + |
        Logger::info("checking models files - init db cache models ...");
        DBCachesModelInitializer::Init($this->m_tableInfo, $force);
        igk_hook(IGKEvents::HOOK_DB_CACHES_INITIALIZED, []);     
    }
    /**
     * update definition 
     * @param BaseController $controller 
     * @param bool $storeCache 
     * @return object 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public static function Update(BaseController $controller, $storeCache = false)
    {
        $init = new DatabaseInitializer;
        $definition = $init->init($controller) ?? (object)["tables" => []];
        $v_i = static::getInstance();
        $v_i->m_tableInfo = array_merge($v_i->m_tableInfo, $definition->tables);
        if ($storeCache) {
            self::CacheData($v_i->m_tableInfo);
        }
        return $definition;
    }
    /**
     * get stored controller table info definitions
     * @return array 
     */
    public function getDefs()
    {
        return $this->m_db_defs;
    }
    /**
    * resolve according to criteria
    * @param string $table
    * @param null|BaseController $controller
    * @param mixed & $table_info
    * @throws IGKException
    * @return mixed
    */
    public function resolve(string $table, ?BaseController $controller = null, & $table_info = null)
    {
        if ($this->m_initializing) {
            if (!isset($this->m_mock[$table])) {
                $mockingData = new DBCacheMockingData($table, $controller);
                $mockingData->defTableName = $table;
                if ($controller) {
                    $mockingData->defTableName = DbUtils::ResolvDefTableTypeName($table, $controller);
                }
                $this->m_mock[$table] = $mockingData;
            }
            $table_info = $this->m_mock[$table];
            return $table_info->tableRowReference;
        }
        !$this->m_init_cache && $this->_initDbCache();
        /**
         * @param $ref_def 
         */
        $ref_def = igk_getv($this->m_tableInfo, $table);
        if (!$ref_def) {
            // + | --------------------------------------------------------------------
            // + | maybe not already loaded - try load definition
            // + |
            $this->m_db_init_request = true;
            $requests_def = null;
            $v_initd = $controller && $controller->getCanInitDb();
            if ($v_initd) {
                $db = new DatabaseInitializer;
                $definition = $db->init($controller);
                if (isset($definition->tables[$table])) {
                    $this->m_tableInfo = array_merge($this->m_tableInfo, $definition->tables);
                    $requests_def = $definition->tables[$table];
                    self::CacheData($this->m_tableInfo);
                }
            }
            $this->m_db_init_request = false;
            if (!$requests_def) {
                Logger::danger('table not found : ' . $table);
            }
            return $requests_def;
        }
        if (empty($ref_def->tableRowReference) && $ref_def->columnInfo) {
            // + | update data with table's row model reference info
            $ref_def->tableRowReference = igk_array_object_refkey($ref_def->columnInfo, IGK_FD_NAME);
        }
        if (empty($ref_def->modelClass) && $ref_def->controller){
            // + | retrieve the controller attaached
            $ref_def->modelClass = IGKSysUtil::GetModelTypeName($ref_def->defTableName, $ref_def->controller);
        }
        $table_info = $ref_def;
        return $ref_def->tableRowReference;
    }
    /**
    * auto generate doc.
    * @param mixed $m
    * @return mixed
    */
    private static function _UnsetPropertiesDefinition($m)
    {
        if (is_object($m)) {
            $m = (array)$m;
        }
        $keys = array_keys((array)$m);
        $var = get_class_vars(SchemaMigrationInfo::class);
        unset($var['definitionResolver']);
        $allowed = array_keys($var); 
        foreach ($keys  as $p) {
            if (!in_array($p, $allowed)) {
                unset($m[$p]);
            }
        }
        return $m;
    }
    /**
    * auto generate doc.
    * @param array $data
    * @return void
    */
    public static function CacheData(array $data)
    {
        $g =  (object)['m_serie' => []];
        $k = null;
        foreach ($data as $k => $m) {
            $cl = $m->controller;
            if (is_null($cl)) {
                igk_dev_wln_e("CacheData: missing controller");
                continue;
            }
            $ad = $cl->getDataAdapterName();
            $cl_name = self::GetClassKeyEntryDefinition($cl);
            $m = self::_UnsetPropertiesDefinition((array)$m);
            if (!isset($g->m_serie[$ad]))
                $g->m_serie[$ad] = [];
            if (!isset($g->m_serie[$ad][$cl_name])) {
                $g->m_serie[$ad][$cl_name] = [];
            }
            $g->m_serie[$ad][$cl_name][$k] = $m;
        } // serie
       //  $g->m_serie[$ad] = [$cl_name=>[$k=>$m]]; 
        $src = serialize(json_decode(Utility::TO_JSON(
            [$g->m_serie, 
            'generate' => date('Ymd His')],[
                'ignore_empty' => true,
                'ignore_null' => true,
            ]
        )));
        $cache_file =self::GetCacheFile();
        igk_io_w2file($cache_file, $src);  
        // unlink($cache_file);
    }
    /**
    * Returns Class Key Entry Definition.
    * @param BaseController $controller
    */
    public static function GetClassKeyEntryDefinition(BaseController $controller){
        if ($controller instanceof ApplicationModuleController){
            $cl_name = "#ref-module(".$controller->getName().")";
        }
        else if (method_exists($controller,$fc= EntryClassResolution::ControllerReferenceInjectorMethod)){
            $injector = $controller::$fc();
            $cl_name = $injector->serializeController($controller);
        }
        else 
            $cl_name = get_class($controller);
        return $cl_name;
    }
    /**
     * clear controller caching - data
     * @param BaseController $controller 
     * @return void 
     */
    public static function ClearControllerCache(BaseController $controller)
    {
        $v_i = self::getInstance();
        if (!$v_i->m_init_cache) {
            DbSchemas::ClearControllerSchema($controller);
            $v_i->_initDbCache();
        }
        // + | --------------------------------------------------------------------
        // + | get database that match controller 
        // + |
        $cl = get_class($controller);
        unset($v_i->m_db_defs->$cl);
        $v_tabinfo = &$v_i->m_tableInfo;
        $v_tabinfo = array_filter(array_map(function ($d) use ($controller) {
            if ($d->controller == $controller) {
                return null;
            }
            return $d;
        }, $v_tabinfo));
        // + | force reload controller schema 
    }
    /**
    * resolv and init tbinfo
    * @param string $tb
    * @param mixed & $tbinfo
    * @throws IGKException
    * @return bool
    */
    public static function ResolvAndInitDbTableCacheInfo(string $tb, & $tbinfo){
        if ($tbinfo = DBCaches::GetTableInfo($tb, null)) {
            $tables[$tb] = $tbinfo;
            if (!$tbinfo->modelClass) {
                $tbinfo->modelClass = IGKSysUtil::GetModelTypeName($tbinfo->defTableName, $tbinfo->controller);
            }
            return true;
        } 
        return false;
    }
}