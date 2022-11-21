<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnvControllerCacheDataBase.php
// @date: 20220906 11:19:26
namespace IGK\System\Caches;

use Exception;
use IGK\ApplicationLoader;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController; 
use IGK\Database\DbSchemaDefinitions;
use IGK\Helper\Utility;
use IGK\System\Database\SchemaMigrationInfo;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKException;
use IGKModuleListMigration;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Caches
 */
class EnvControllerCacheDataBase
{

    // + | --------------------------------------------------------------------
    // + | ALL THE DATA SERIALIZED
    // + | m_serie[$n][$cl] where $n = the adapter name , $cl class name that control
    ///+ | the value , = $tableInfo definition
    
    const FILE = DbSchemaDefinitions::CACHE_FILE;
    var $file;
    private $m_preload;
    private $m_controllers;
    private $m_serie = [];
    private static $sm_localRef;
    private static $sm_initialized;

    /**
     * reset and clear the db cache
     * @return void 
     */
    public static function ResetCache(){
        $file = self::GetCacheFile();
        if (file_exists($file)){
            @unlink($file);
        }
        $q = self::$sm_localRef ?? new self($file);
        $q->m_serie = [];
        self::$sm_initialized = false;
    }

    public function __construct(?string $file, ?SysDbController $sysdb = null)
    {
        igk_trace();
        igk_wln_e("lkj");
        $sysdb = $sysdb ?? SysDbController::ctrl();
        $this->file = $file;
        $this->m_controllers = [$sysdb];
        self::$sm_localRef = $this;
    }
    public function update(BaseController $controller)
    {
        if (!$controller->getCanInitDb()) {
            return;
        }
        $this->m_controllers[] = $controller;
        // igk_wln("update : ", get_class($controller), $controller->getDeclaredFileName());
    }
    /**
     * static initialize DbManager
     * @return self 
     * @throws Exception 
     * @throws IGKException 
     */
    // public static function Init(callable $callback, string $adapterName , string $table, string $resolvKey ) : self
    // {
    //     if (self::$sm_initialized) {
    //         $q = self::$sm_localRef;
    //         if ($q->m_preload){
    //             $q->m_preload->resolv($callback, $adapterName, $table);
    //         }  
    //         return $q;
    //     }
    //     $q = self::$sm_localRef ?? new self(self::_GetCacheFile(), SysDbController::ctrl());
    //     $b = IGKModuleListMigration::CreateModulesMigration();
    //     $q->update($b);
    //     self::$sm_initialized = true;
        
    //     $q->m_preload = new EnvControllerInitControllers($q, 
    //         $callback,
    //         $q->m_controllers,
    //         $adapterName,
    //         $table,
    //         $resolvKey);
    //     $q->m_preload->init(); 
    //     return $q;
    // }
    /**
     * get table info from db cache
     * @param string $classname 
     * @param string $adname 
     * @param string $table 
     * @return mixed 
     * @throws IGKException 
     */
    // public function getTableInfo(string $classname, string $adname, string $table){
    //     if (isset($this->m_serie[$adname][$classname])){
    //         return igk_getv($this->m_serie[$adname][$classname]->tables, $table);
    //     }
    // }
    /**
     * get seri data
     * @return mixed 
     */
    public function getSerie(){
        return $this->m_serie;
    }
    /**
    //  * load controller table definition
    //  * @param BaseController $controller 
    //  * @return void 
    //  * @throws Exception 
    //  */
    // public function loadDef(BaseController $controller, $init=false){
    //     // on init context must migrate module manager
    //     // if ($init){
    //     //     if ($controller instanceof IGKModuleListMigration){
    //     //         $controller::InitMigration();
    //     //         return;
    //     //     }
    //     // }
    //     if ($def = $controller->getDataTableDefinition()) {
    //         $cl = get_class($controller);
    //         $n = $controller->getDataAdapterName();
    //         if (!isset($this->m_serie[$n])) {
    //             $this->m_serie[$n] = [];
    //         }
    //         $to_seri = (object)["tables" => []];
    //         // unset controller for storage
    //         foreach ($def->tables as $k => $v) {
    //             $m = (array)$v;
    //             unset($m["controller"]);
    //             unset($m["entries"]);
    //             unset($m["tableRowReference"]);
    //             $to_seri->tables[$k] = $m;
    //         }
    //         $this->m_serie[$n][$cl] = json_decode(Utility::TO_JSON($to_seri, [
    //             'ignore_empty' => 1
    //         ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    //     }
    // }
  
    // /**
    //  * complete
    //  * @return void 
    //  * @throws IGKException 
    //  */
    // public function complete()
    // {
    //     if ($this->file) {
    //         register_shutdown_function(
    //             function () {
    //                 if (self::$sm_initialized){
    //                     $this->_storeCache();
    //                 }
    //             }
    //         );
    //     }
    // }
    // private function _storeCache($cacheOnly = true)
    // {
    //     if ($cacheOnly && $this->m_controllers && !self::$sm_initialized) {
    //         foreach ($this->m_controllers as $c) {
    //             $this->loadDef($c);
    //         }
    //     }
    //     // + | --------------------------------------------------------------------
    //     // + | SANITIZE before serialia
    //     // + |        
    //     foreach($this->m_serie as $n=>$v){            
    //         foreach($v as $cl=>$kt){
    //             $tables = (array)$kt->tables;
    //             $to_seri = array_map([self::class, '_UnsetPropertiesDefinition'], (array)$tables);
    //             $this->m_serie[$n][$cl]->tables = json_decode(Utility::TO_JSON($to_seri, [
    //                 'ignore_empty' => 1
    //             ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    //         }
    //     }
    //     igk_io_w2file($this->file, serialize($this->m_serie));
    // }
    private static function _UnsetPropertiesDefinition($m){
        if (is_object($m)){
            $m = (array)$m;
        }
        // unset property that is not on the list
        $keys = array_keys((array)$m);
        $var = get_class_vars(SchemaMigrationInfo::class);
        $allowed = array_keys($var); // [ 'columnInfo', 'defTableName', 'description', 'tableName', 'defTableName'];
        foreach($keys  as $p){
            if (!in_array($p, $allowed)){
                unset($m[$p]);
            }
        } 
        // unset($m["controller"]);
        // unset($m["entries"]);
        // unset($m["tableRowReference"]);
        // unset($m["context"]);
        // unset($m["modelClass"]);
        return $m;
    }
    public static function CacheData($data){
        $g = self::$sm_localRef ?? new self(self::GetCacheFile());
        $g->m_serie = [];
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
        igk_io_w2file($g->file, serialize(json_decode(Utility::TO_JSON($g->m_serie, [
            'ignore_empty' => 1
        ]))));
    }
    public static function GetCacheFile(){
        return igk_io_cachedir() . "/" . self::FILE;
    }
    /**
     * store cache
     * @param bool $update 
     * @return void 
     * @throws Exception 
     * @throws IGKException 
     */
    // public static function StoreCache(bool $update = false)
    // {
    //     if ($g = self::$sm_localRef) {
    //         self::$sm_localRef->_storeCache(!$update);
    //     } else {
    //         $g = new self(self::_GetCacheFile(), SysDbController::ctrl());
    //         if (!$update) {
    //             $manager = igk_app()->getControllerManager();
    //             $loader = ApplicationLoader::getInstance();
    //             $c = new InitEnvControllerChain;
    //             $tab =  EnvControllerCacheList::GetControllersClasses();
    //             $c->add($g);
    //             $c->load($tab, $manager, $loader);
    //         }
    //         $g->_storeCache(!$update);
    //     }
    // }
    /**
     * 
     * @param BaseController $controller 
     * @param array $def definition to update
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    // public static function UpdateCache(BaseController $controller, array $def)
    // {
    //     if ($g = self::$sm_localRef) {
    //         $ad_name = $controller->getDataAdapterName();
    //         foreach ($def  as $table => $definition) {
    //             $g->_updateSerie($ad_name, $table, $definition);
    //         }
    //         $g->_storeCache(false);
    //     }
    // }
    /**
     * 
     * @param mixed $adapterName 
     * @param mixed $def 
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     */
    // private function _updateSerie(string $adapterName, string $table, $definition)
    // {
    //     if (is_array($definition)) {
    //         $definition = (object)$definition;
    //     }
    //     if (!isset($definition->controller)){
    //         return;
    //     }
    //     $n = $adapterName;
    //     $cl = is_object($definition->controller) ? 
    //         get_class($definition->controller): $definition->controller;
    //     if (!isset($this->m_serie[$n])) {
    //         $this->m_serie[$n] = [];
    //     }
    //     $de =igk_getv($this->m_serie[$n], $cl); 
    //     $to_seri = (object)["tables" => (array) ($de ? igk_getv($de, 'tables') : null) ?? [] ];
    //     $tv = $definition;
    //     $m = (array)$tv;
    //     $m = self::_UnsetPropertiesDefinition($m);
    //     // unset($m["controller"]);
    //     // unset($m["entries"]);
    //     // unset($m["tableRowReference"]);
    //     // unset($m["context"]);
    //     $to_seri->tables[$table] = $m;
    //     $this->m_serie[$n][$cl] = json_decode(Utility::TO_JSON($to_seri, [
    //         'ignore_empty' => 1
    //     ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    // }
    // /**
    //  * register to environment controller chain
    //  * @param InitEnvControllerChain $c 
    //  * @return void 
    //  * @throws IGKException 
    //  */
    // public static function RegisterToChain(InitEnvControllerChain $c):bool{
    //     $file = self::_GetCacheFile();
    //     if (!is_file($file) || (filesize($file)==0)){
    //         $sysdb = igk_getctrl(SysDbController::class);
    //         $c->add(new EnvControllerCacheDataBase($file, $sysdb));
    //         return true;
    //     }
    //     return false;
    // }
}
