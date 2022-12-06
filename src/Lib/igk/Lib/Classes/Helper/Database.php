<?php
// @author: C.A.D. BONDJE DOUE
// @file: Database.php
// @date: 20221119 00:06:15
namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Database\DbSchemaDefinitions;
use IGK\Database\DbSchemasConstants;
use IGK\System\Models\IModelDefinitionInfo;
use IGK\System\Caches\DBCaches;
use IGK\System\Database\DatabaseInitializer;
use IGK\System\Database\MigrationHandler;
use IGK\System\DataBase\SchemaBuilderHelper;
use IGKEvents;
use IGKModuleListMigration;
use IGKType;
use Illuminate\Database\Migrations\Migrator;
use MenuDefinition;
use ReflectionMethod;

use function igk_resources_gets;
///<summary></summary>
/**
* 
* @package IGK\Helper
*/
class Database{
    static $sm_shared_info;

    private static function _Init(){
        self::$sm_shared_info = [];
    }
    public static function GetInfo($n){
        return igk_getv(self::$sm_shared_info, $n);
    }
    public static function InitSystemDb(){
        self::_Init();    
        DBCaches::Reset();
        $tables = DBCaches::GetCacheData();
        self::$sm_shared_info = $tables;
        $sysctrl = SysDbController::ctrl();
        $ad_name = $sysctrl->getDataAdapterName();
 
        $dbinitializer = new DatabaseInitializer;
        $dbinitializer->resolv = $ad_name;     
        $dbinitializer->upgrade($sysctrl, $tables, DBCaches::GetCacheInitializer());       
       
        self::$sm_shared_info = [];
        unset($dbinitializer);
    }
    public static function CreateTableBase(BaseController $controller, $tb, $etb=null){
        $ctrl = $controller;
        if (is_null(($adapter = $controller->getDataAdapter()))){
            igk_dev_wln_e("why is null ".$controller);
            return;
        }
        if (!$adapter->getIsConnect()){
            return;
        }
        $adapter->beginInitDb($ctrl);
        foreach ($tb as $k => $v) {
            if (is_numeric($k)){
                $k = $v->defTableName;
            }
            $n = igk_db_get_table_name($k, $ctrl);
            self::$sm_shared_info[$n] = $v;
            $data = $etb ? igk_getv($etb, $n) : null;
            igk_hook(IGK_NOTIFICATION_INITTABLE, [$ctrl, $n, &$data]);
            $columnInfo = $v->columnInfo;
            if (!$adapter->createTable($n, $columnInfo, $data, $v->description, $adapter->DbName)) {
                igk_push_env("db_init_schema", sprintf("failed to create  : %s", $n));               
                igk_ilog("failed to create " . $n);
            }
        }
        $adapter->endInitDb();
        igk_hook(IGKEvents::HOOK_DB_POST_GROUP, [
            'ctrl'=>$controller
        ]);
    }

       /**
     * only for system an core
     * @param BaseController $controller 
     * @param array $definition table definition 
     * @param bool $force force to init logic
     * @return void 
     */
    public static function InitDbCoreLogic(BaseController $controller, $definitions, bool $force )
    {        

        SchemaBuilderHelper::Migrate($definitions);
        // + | ------------------------------------------------------------------------------------
        // + | init constant file 
        // + |
        
        $controller->initDbConstantFiles();

        // + | ------------------------------------------------------------------------------------
        // + | init database model 
        // + |        

        $controller->InitDataBaseModel($definitions, $force);
    }
    public static function InitDataEntries(BaseController $controller){

        //initialise manager 
        // + | --------------------------------------------------------------------
        // + | BEFORE INIT - APPLICATION
        // + |
        if (!(($cl = $controller::resolvClass('Database/DbInitManager')) && class_exists($cl, false)
            && is_subclass_of($cl, \IGK\Database\DbInitManager::class))) {
            $cl = \IGK\Database\DbInitManager::class;
        }
        if ($cl) {
            (new $cl($controller))->init($controller);
        }
        if (($cl = $controller::resolvClass('Database/InitData')) && class_exists($cl, false)) {
            $call = true;
            // + | Check Init 
            $c = new ReflectionMethod($cl, "Init");
            if ($type = $c->getParameters()[0]->getType()) {
                $call = IGKType::GetName($type) == get_class($controller);
            }
            $call && $cl::Init($controller);
        }
    }
}