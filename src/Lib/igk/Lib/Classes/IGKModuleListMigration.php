<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKModuleListMigration.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\Controllers\ApplicationModuleController;
use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerExtension;
use IGK\Controllers\IGlobalModelFileController;
use IGK\Database\DbSchemasConstants;
use IGK\IDbGetTableReferenceHandler;
use IGK\System\Caches\DBCaches;
use IGK\System\Console\Logger;
use IGK\System\Database\DatabaseInitializer;
use IGK\System\Database\IDbMigrationMethods;
use IGK\System\Database\MigrationHandler;
use IGK\System\Database\Traits\DbCreateTableReferenceTrait;
use IGK\System\Exceptions\ArgumentTypeNotValidException;

/**
 * single use class pattern 
 * @package 
 */
final class IGKModuleListMigration extends BaseController implements
    IDbGetTableReferenceHandler,
    IGlobalModelFileController,
    IDbMigrationMethods
{
    use DbCreateTableReferenceTrait;
    /**
     * make it participate to loading and migration
     * @return bool 
     */
    public function getUseDataSchema(): bool
    {
        return true;
    }
    /**
     * Collection of list.
     * @var mixed
     */
    private static $sm_list;
    /**
     * module list instance 
     * @var mixed
     */
    private static $sm_instance;
    /**
     * Property: host.
     * @var mixed
     */
    private $m_host;
    /**
     * Collection of list.
     * @var mixed
     */
    private $m_list;
    /**
     * Property: loaded.
     * @var mixed
     */
    private $m_loaded = [];
    /**
     * Property: definition.
     * @var mixed
     */
    private $m_definition;
    /**
     * Property: initializer.
     * @var mixed
     */
    private $m_initializer;
    /**
     * .ctr
     * @return mixed
     */
    private function __construct() {}
    /**
     * auto generate doc.
     * @return void
     */
    public function useAsInstance()
    {
        if (self::$sm_instance === $this) {
            return;
        }
        self::$sm_instance = $this;
    }
    /**
     * Resets.
     */
    public function reset()
    {
        self::$sm_instance = null;
    }
    /**
     * migrate list :
     * @param mixed $callback 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function migrateHost(closure $callback)
    {
        if ($this->m_host->getUseDataSchema()) {
            $file = $this->m_host->getDataSchemaFile();
            if (igk_io_file_exists($file)) {
                if (igk_is_debug()) {
                    Logger::warn('migrate list ..... ' . $this->m_host->getName());
                    Logger::info($file);
                }
                $data = igk_db_load_data_schemas($file, $this, true, DbSchemasConstants::Migrate);
                if ($mig = igk_getv($data, 'migrations')) {
                    foreach ($mig as $m) {
                        $ctrl = $m->controller;
                        $m->controller = $this;
                        $m->upgrade();
                        $m->controller = $ctrl;
                    }
                }
            }
        }
    }
    protected function getControllerFromTableInfo(string $table)
    {
        /**
         * @var mixed
         */
        $tbinfo = null;
        if (DBCaches::ResolvAndInitDbTableCacheInfo($table, $tbinfo)) {
            $ctrl = $tbinfo->controller;
            return $ctrl;
        }
    }
    /**
     * Db add column.
     * @param string $table
     * @param mixed $columnInfo
     * @param null|string $after
     */
    public function db_add_column(string $table, $columnInfo, ?string $after = null)
    {
        if ($ctrl = $this->getControllerFromTableInfo($table)) {
            $ctrl->db_add_column($table, $columnInfo, $after);
        };
    }
    /**
     * Db rm column.
     * @param string $table
     * @param mixed $columnInfo
     */
    public function db_rm_column(string $table, $columnInfo)
    {
        if ($ctrl = $this->getControllerFromTableInfo($table)) {
            $ctrl->db_rm_column($table, $columnInfo);
        };
    }
    /**
     * migrate loaded list
     * @return void 
     */
    public function migrateList()
    {
        $handler = new MigrationHandler($this);
        foreach ($this->m_list as $ctrl) {
            Logger::info("migrate .... " . $ctrl->getName());
            $this->m_host = $ctrl;
            try {
                if (ControllerExtension::migrate($this)) {
                    $handler->up();
                }
            } catch (Exception $ex) {
                Logger::danger("error ... " . $ex->getMessage());
                return false;
            }
        }
    }
    /**
     * module need to implement this method to inject database with host as static function 
     * @return mixed 
     */
    public function injectBaseModel()
    {
        if ($this->m_host && method_exists($this->m_host, __FUNCTION__)) {
            return call_user_func_array([$this->m_host, __FUNCTION__], []);
        }
    }
    /**
     * Handles Model Creation.
     * @param mixed $table_list
     * @return bool
     */
    public function handleModelCreation($table_list): bool
    {
        if ($this->m_host && method_exists($this->m_host, __FUNCTION__)) {
            return call_user_func_array([$this->m_host, __FUNCTION__], [$table_list]);
        }
        return true;
    }
    /**
     * get host model
     * @return mixed 
     */
    public function getHost()
    {
        return $this->m_host;
    }
    /**
     * schema migration list 
     * @param array $list 
     * @return static 
     */
    public static function Create(array $list)
    {
        $g = new self();
        $g->m_list = $list;
        return $g;
    }
    /**
     * create a molude migration context
     * @return static|null 
     * @throws IGKException 
     */
    public static function CreateModulesMigration()
    {
        if ($modules = igk_get_modules()) {
            $list = array_filter(array_map(function ($c, $k) {
                if ($mod = igk_get_module($k)) {
                    return $mod;
                }
            }, $modules, array_keys($modules)));
            return self::Create($list);
        }
        return null;
    }
    /**
     * Get modules.
     */
    static function _GetModules()
    {
        if (is_null(self::$sm_list)) {
            self::$sm_list = array_filter(array_map(function ($a) {
                if ($mod = igk_get_module($a->name)) {
                    return $mod;
                } else {
                    igk_ilog('missing module ' . $a->name);
                }
            },  igk_get_modules() ?? []));
        }
        return self::$sm_list;
    }
    /**
     * Returns Classes Dir.
     */
    public function getClassesDir()
    {
        return $this->m_host->getClassesDir();
    }
    /**
     * Returns Entry Name Space.
     */
    public function getEntryNameSpace()
    {
        return $this->m_host->getEntryNamespace();
    }
    /**
     * auto generate doc.
     * @param string $path
     * @return string|void
     */
    public static function ns(string $path = '')
    {
        $sm = self::$sm_instance;
        if ($sm) {
            return ControllerExtension::ns($sm->m_host, $path);
        } else {
            igk_die("can't get namespace : instance not created");
        }
    }
    /**
     * migrate install - migration 
     * @return bool 
     */
    public static function Migrate()
    {
        Logger::info("Modules migration...");
        self::$sm_instance = new self();
        $v_modules = self::_GetModules();
        if ($v_modules) {
            $handler = new MigrationHandler(self::$sm_instance);
            foreach ($v_modules as $l) {
                Logger::info("migrate .... " . $l->getName());
                self::$sm_instance->m_host = $l;
                try {
                    if (ControllerExtension::migrate(self::$sm_instance)) {
                        $handler->up();
                    }
                } catch (Exception $ex) {
                    Logger::danger("error ... " . $ex->getMessage());
                    return false;
                }
            }
        } else {
            Logger::warn('modules list is empty.');
        }
        return true;
    }
    /**
     * wrapper
     * @param bool $navigate 
     * @param bool $force 
     * @return true 
     * @throws IGKException 
     */
    public static function resetDb($navigate = false, $force = false)
    {
        self::$sm_instance = new self();
        $fc = BaseController::getMacro("resetDb");
        $v_modules = self::_GetModules();
        foreach ($v_modules as $l) {
            Logger::info("reset module db .... " . $l->getName());
            self::$sm_instance->m_host = $l;
            $fc(self::$sm_instance, $navigate, $force);
            self::$sm_instance->m_host = $l;
            ControllerExtension::migrate(self::$sm_instance);
        }
        return true;
    }
    /**
     * auto generate doc.
     * @param mixed $method
     * @param mixed $navigate
     * @param bool $force
     * @param ?array $modules
     * @return void
     */
    private static function _InvokeExtension($method, $navigate = false, $force = false, ?array $modules = null)
    {
        self::$sm_instance = new self();
        $v_modules = $modules ?? self::_GetModules();
        if (($fc = BaseController::getMacro($method)) && $v_modules) {
            foreach ($v_modules as $l) {
                $n = $l->getName();
                Logger::info(" module db .... [ " . $method . ' ] > ' . $n);
                self::$sm_instance->m_host = $l;
                $fc(self::$sm_instance, $navigate, $force);
                self::$sm_instance->m_host = $l;
                Logger::info('call migrate');
                ControllerExtension::migrate(self::$sm_instance);
            }
        }
    }
    /**
     * override 
     * @param mixed $n 
     * @param mixed $argument 
     * @return mixed|void 
     */
    public function __call($n, $argument)
    {
        if ($this->m_host) {
            return call_user_func_array([$this->m_host, $n],  $argument);
        } else {
            if (is_null($this->m_list)) {
                return;
            }
            foreach ($this->m_list as $h) {
                $p = $argument;
                if (method_exists($h, $n)) {
                    $h->$n(...$p);
                    continue;
                }
                if (method_exists(ControllerExtension::class, $n)) {
                    array_unshift($p, $h);
                    ControllerExtension::$n(...$p);
                }
            }
        }
    }
    /**
     * auto generate doc.
     * @param mixed $name
     * @param mixed $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(ControllerExtension::class, $name)) {
            if (isset(self::$sm_instance->host)) {
                array_unshift($arguments, self::$sm_instance->host);
                return ControllerExtension::$name(...$arguments);
            } else {
                Logger::warn("no host defined");
            }
        }
        return null;
    }
    /**
     * can init database
     * @return bool 
     */
    public function getCanInitDb(): bool
    {
        return true;
    }
    /**
     * Registers autoload.
     */
    public function register_autoload() {}
    /**
     * Drops Db.
     * @param mixed $navigate
     * @param mixed $force
     */
    public static function dropDb($navigate = 1, $force = 0)
    {
        self::_InvokeExtension(__FUNCTION__, $navigate, $force);
    }
    /**
     * initialize module database 
     * @param bool $force 
     * @return never 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function initDb($force = true)
    {
        self::_InvokeExtension(__FUNCTION__, $force);
    }
    /**
     * Initializes Db Module.
     * @param bool $force
     * @param array $modules
     */
    public static function InitDbModule(bool $force, array $modules)
    {
        self::_InvokeExtension('initDb', false, $force, $modules);
    }
    /**
     * no table definition for migration
     * @return null 
     */
    public function getDataTableDefinition()
    {
        return null;
    }
    /**
     * module must only run a migration process
     * @return void 
     */
    public static function InitMigration()
    {
        if (!count(self::$sm_list)) {
            return;
        }
        // + | --------------------------------------------------------------------
        // + | reset database
        // + |            
        IGKModuleListMigration::resetDb(false, true);
        // + | --------------------------------------------------------------------
        // + | migrate module to use data if active
        // + |            
        IGKModuleListMigration::Migrate();
    }
    /**
     * resolve class 
     * @param string $path 
     * @return mixed 
     */
    public function resolveClass(string $path)
    {
        if ($this->m_host) {
            return $this->m_host->resolveClass($path);
        }
        return null;
    }
    /**
     * downgrade 
     * @return void 
     */
    public static function downgrade()
    {
        self::$sm_instance = new self();
        $v_modules = self::_GetModules();
        $idx = 0;
        if ($v_modules) {
            foreach ($v_modules as $s => $t) {
                if ($t instanceof BaseController) {
                    self::$sm_instance->m_host = $t;
                    $c = new MigrationHandler($t);
                    $c->down();
                    if (igk_io_file_exists($file = $t->getDataSchemaFile())) {
                        igk_db_load_data_schemas($file, self::$sm_instance, true, DbSchemasConstants::Downgrade);
                    }
                } else {
                    igk_json(['error' => 'missing modules info at ' . $idx . ' : ' . $s]);
                }
                $idx++;
            }
        }
    }
    /**
     * Returns Data Schema File.
     */
    public static function getDataSchemaFile()
    {
        if (self::$sm_instance) {
            return self::$sm_instance->m_host->getDataSchemaFile();
        }
    }
    /**
     * schema definition info
     * @param DatabaseInitializer $initializer
     * @param mixed $operation
     * @throws IGKException
     * @return void
     */
    public function loadMigrationSchema(DatabaseInitializer $initializer, $operation = DbSchemasConstants::Migrate)
    {
        $this->m_initializer = $initializer;
        foreach ($this->m_list as $t) {
            if (!($t instanceof ApplicationModuleController) && (!$t->getCanInitDb() || !$t->getUseDataSchema())) {
                continue;
            }
            $adname = $t->getDataAdapterName();
            if (igk_io_cache_file_exists($file = $t->getDataSchemaFile())) {
                $this->m_host = $t;
                $initializer->loadSchemaDefinition(
                    $file,
                    $this,
                    $operation,
                    $t
                );
            }
        }
        $this->m_host = null;
        $this->m_list = null;
        unset($this->m_loaded);
        unset($this->m_list);
        return $this->m_definition;
    }
    /**
     * Resolv table definition.
     * @param string $table
     */
    public function resolvTableDefinition(string $table)
    {
        static $rstable;
        if ($rstable === null) {
            $rstable = [];
        }
        if ($p = igk_getv($rstable, $table)) {
            return $p;
        }
        $tab = $this->m_initializer->definitions[$this->m_initializer->resolv]->tables;
        foreach ($tab as $m) {
            if ($table == $m->tableName) {
                $rstable[$table] = $m;
                return $m;
            }
        }
        return null;
    }
    /**
     * Returns Env Param.
     * @param mixed $key
     */
    public function getEnvParam($key)
    {
        $key = $this->getEnvKey($key);
        return igk_getv($this->m_loaded, $key);
    }
    /**
     * Returns Env Key.
     * @param mixed $key
     */
    public function getEnvKey($key)
    {
        return $this->m_host->getName() . "/" . $key;
    }
    /**
     * get string presentation.
     */
    public function __toString()
    {
        if ($this->m_host) {
            return sprintf(
                "%s - [%s]",
                __CLASS__,
                $this->m_host->getName()
            );
        }
        return __CLASS__;
    }
    /**
     * init module list 
     * @return static
     */
    public static function InitModuleList()
    {
        $modules = igk_get_modules();
        $list = array_filter(array_map(function ($c, $k) {
            if ($mod = igk_get_module($k)) {
                return $mod;
            }
        }, $modules, array_keys($modules)));
        if ($list) {
            return self::Create($list);
        }
        return null;
    }
}
