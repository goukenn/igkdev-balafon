<?php
// @author: C.A.D. BONDJE DOUE
// @file: DBCaches.php
// @date: 20221119 11:34:09
namespace IGK\System\Caches;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerExtension;
use IGK\Controllers\SysDbController;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbSchemas;
use IGK\Helper\Activator;
use IGK\Helper\Database;
use IGK\Helper\Utility;
use IGK\System\Console\Logger;
use IGK\System\Database\DatabaseInitializer;
use IGK\System\Database\DbUtils;
use IGK\System\Database\SchemaMigrationInfo;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\NotImplementException;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
* 
* @package IGK\System\Caches
*/
class DBCaches{
    const CACHE_FILE_NAME = '.data-schema.definition.cache';
    
    private static $sm_instance;

    private $m_db_initializer;

    private $m_db_init_request;

    /**
     * store controller loaded definition
     * @var array
     */
    private $m_db_defs = [];

    /**
     * init db request 
     * @return mixed 
     */
    public static function InitRequest(){
        return self::getInstance()->m_db_init_request;
    }

    public static function GetCacheFile(){
        return igk_io_cachedir().'/'.self::CACHE_FILE_NAME;
    }


    public static function GetCacheData(){
        return self::getInstance()->m_tableInfo;
    }
    public static function GetCacheInitializer(){
        return self::getInstance()->m_db_initializer;
    }
    /**
     * 
     * @return mixed 
     */
    public static function IsInitializing(){
        return self::getInstance()->m_init;
    }
    /**
     * table register according to system management database
     * @var array
     */
    private $m_tableInfo = []; 

    private $m_init = false;

    private $m_initializing = false;
    /**
     * get the dbcache instances
     * @return static
     */
    public static function getInstance(){
        if (self::$sm_instance === null)
            self::$sm_instance = new static;
        return self::$sm_instance;
    }
    public static function Init(){
        $i = self::getInstance();
        if ($i->m_init){
            return;
        }
        $i->_initDbCache();        
    }
    /**
     * reset the db cache info
     * @return never 
     * @throws NotImplementException 
     */
    public static function Reset(){
        static::getInstance()->_clearAndReload();        
    }
    /**
     * retrieve cached table info
     * @param string $table 
     * @param null|BaseController $controller 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetInfo(string $table, ?BaseController $controller= null){
        return static::getInstance()->resolve($table, $controller);
    }
    /**
     * get table information
     */
    public static function GetTableInfo(string $table, ?BaseController $controller= null){
        $c = igk_getv(static::getInstance()->m_tableInfo, $table);
        if ($controller && $c){
            // + | --------------------------------------------------------------------
            // + | check matching 
            // + |
            if ($controller != $c->controller){
                igk_die("db retrieve but controller not match");
            }
            
        }
        return $c; 
    }
    public static function Get(string $n){
        $g = static::getInstance();
        return igk_getv($g->m_tableInfo, $n);
    }
    public static function Register(string $table,  $info){
        $g = static::getInstance();
        $g->m_tableInfo[$table] = $info;
    }
    public static function Clear(){
        static::getInstance()->_clear();
    }
    private function __construct(){       
    }
    public function __toString()
    {
        return 'Systemp - DB Cache';
    }
    private function _clear(){
        $this->m_tableInfo = [];
    }
    private function _clearAndReload(){
        $this->_clear();
        $this->m_init = false;
        if (file_exists($file = self::GetCacheFile()))
            @unlink($file);

        DbSchemas::ResetSchema();
        $this->_initDbCache();
    }
    protected function _initDbCache(){
        if ($this->m_initializing){
            return;
        }
        // + | --------------------------------------------------------------------
        // + | cache is empty - load from cache -  
        // + |
        $sysctrl = SysDbController::ctrl();        
        if (!$this->m_init){
            if (is_file($file = self::GetCacheFile())){
                $data = unserialize(file_get_contents($file));
                if ($data !== false){
                    $ad_name = $sysctrl->getDataAdapterName();
                    $trdata = igk_getv($data->{'0'}, $ad_name);
                    $rdata = [];
                    if (!$trdata){

                        igk_wln_e("data---:' not found ", $trdata);
                    }
                    foreach($trdata as $ctrl => $v){
                        if (!($gctrl = igk_getctrl($ctrl, false))){
                            continue;
                        }
                        foreach($v as  $d)
                        { 
                        $rdata[$d->tableName] = Activator::CreateNewInstance(SchemaMigrationInfo::class,  [
                            'columnInfo'=>array_map(function($a){
                                    return Activator::CreateNewInstance(DbColumnInfo::class, $a);
                                }, (array)$d->columnInfo),
                            'description'=> igk_getv($d, 'description'),
                            'defTableName'=>igk_getv($d, 'defTableName'),
                            'controller'=>$gctrl,
                            'tableName'=>$d->tableName,
                            'definitionResolver'=>null
                            ]);
                        }
                    }
                    $this->m_tableInfo = $rdata; 
                    $this->m_db_defs =  $trdata;
                    $this->m_init = 1;
                    return;
                }
            }
        } 
        // initialize system controller
        $this->m_init = 1;
        $this->m_initializing = 1;
        $this->m_db_init_request = 1;
        igk_environment()->NO_PROJECT_AUTOLOAD = 1;
        $db = new DatabaseInitializer; 
        $definition = $db->init($sysctrl);
        $this->m_tableInfo = $definition->tables; //  array_combine(array_keys((array)$definition->tables) ,$definition->tables) ; 
        $this->m_db_initializer = $db;
        // init project definition  
        $db->loadSystemProjects();
        // update with module 
        $db->loadSystemModules();

        // table definition - 
        foreach($db->getDefs() as $p){
            list($ctrl, $info) =  $p;
            if ($ctrl == $sysctrl)
            continue;
        
            foreach($info->tables as $tablen=>$info){ 
                if (is_numeric($tablen)){                   
                    $tablen = $info->tableName;
                }                
                if (key_exists($tablen, $this->m_tableInfo)){
                    Logger::warn('table will enter in conflict '.$tablen);
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
        // check and init model class 
        $this->m_initializing = false; 
        $this->m_db_init_request= false;
        igk_environment()->NO_PROJECT_AUTOLOAD = null;
    
        // + | --------------------------------------------------------------------
        // + | check and init data model 
        // + |
        Logger::warn("check for data models files");
        DBCachesModelInitializer::Init($this->m_tableInfo);  
        igk_hook('db_caches initialized', []);
        // @igk_ serialize - the data to speed loading         
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
    public static function Update(BaseController $controller, $storeCache=false){
        $init = new DatabaseInitializer;
        $definition = $init->init($controller);
        $v_i = static::getInstance();
        $v_i->m_tableInfo = array_merge($v_i->m_tableInfo, $definition->tables);
        if ($storeCache){
            self::CacheData($v_i->m_tableInfo);
        }
        return $definition;
        
    }
    /**
     * get stored controller table info definitions
     * @return array 
     */
    public function getDefs(){
        return $this->m_db_defs;
    }
    /**
     * resolve according to criteria
     * @param string $table 
     * @param null|BaseController $controller 
     * @return mixed 
     * @throws IGKException 
     */
    public function resolve(string $table, ?BaseController $controller=null){      
        if ($this->m_initializing){
            if (!isset($this->m_mock[$table])){
                $mockingData = new DBCacheMockingData($table, $controller);
                $mockingData->defTableName = $table;
                if ($controller){
                    $mockingData->defTableName = DbUtils::ResolvDefTableTypeName($table, $controller);                    
                }
                $this->m_mock[$table] = $mockingData;
            }
            return $this->m_mock[$table]->tableRowReference; 
        }
        !$this->m_init && $this->_initDbCache();        
        /**
         * @param $ref_def 
         */
        $ref_def = igk_getv($this->m_tableInfo, $table);
        if (!$ref_def){

            // + | --------------------------------------------------------------------
            // + | maybe not already loaded - try load definition
            // + |
            $this->m_db_init_request = true;
            $requests_def = null;
            if ($controller && $controller->getCanInitDb()){

                $db = new DatabaseInitializer; 
                $definition = $db->init($controller);
                if (isset($definition->tables[$table])){
                    // table definition found - 
                    $this->m_tableInfo = array_merge($this->m_tableInfo, $definition->tables);
                    $requests_def = $definition->tables[$table];
                    self::CacheData($this->m_tableInfo);
                }
            }
            $this->m_db_init_request = false;
            if (!$requests_def){
                Logger::danger('table not found : '.$table);
            }
            return $requests_def;
        }
        if (empty($ref_def->tableRowReference)){        
            //
            // + | update data with table's row model reference info
            //
            $ref_def->tableRowReference = igk_array_object_refkey($ref_def->columnInfo, IGK_FD_NAME);
        
        }
        return $ref_def->tableRowReference;

    }

    private static function _UnsetPropertiesDefinition($m){
        if (is_object($m)){
            $m = (array)$m;
        }
     
        $keys = array_keys((array)$m);
        $var = get_class_vars(SchemaMigrationInfo::class);
        $allowed = array_keys($var); // [ 'columnInfo', 'defTableName', 'description', 'tableName', 'defTableName'];
        foreach($keys  as $p){
            if (!in_array($p, $allowed)){
                unset($m[$p]);
            }
        }  
        return $m;
    }
    /**
     * 
     * @param array $data 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public static function CacheData(array $data){
        $g =  (object)['m_serie'=>[]];
        
        foreach($data as $k=>$m){
            $cl = $m->controller;
            if (is_null($cl)){
                igk_wln_e("bad null found");
            }
            $ad = $cl->getDataAdapterName();
            $m = self::_UnsetPropertiesDefinition((array)$m);
            $cl_name = get_class($cl);
            if (!isset($g->m_serie[$ad]))
                $g->m_serie[$ad] = [];
            if (!isset($g->m_serie[$ad][$cl_name])){
                $g->m_serie[$ad][$cl_name] = [];
            }
            $g->m_serie[$ad][$cl_name][$k] = $m;
        }
        igk_io_w2file(self::GetCacheFile(), serialize(json_decode(Utility::TO_JSON(
            [$g->m_serie, 'generate'=>date('Ymd')], [
            'ignore_empty' => 1
        ]))));
    }
}