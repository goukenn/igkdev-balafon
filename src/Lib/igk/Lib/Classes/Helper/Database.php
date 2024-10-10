<?php
// @author: C.A.D. BONDJE DOUE
// @file: Database.php
// @date: 20221119 00:06:15
namespace IGK\Helper;

use Error;
use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Database\ORM\Annotations\InitDataAnnotation;
use IGK\Models\ModelBase;
use IGK\System\Caches\DBCaches;
use IGK\System\Console\Logger;
use IGK\System\Database\DatabaseInitializer;
use IGK\System\Database\SchemaBuilderHelper;
use IGK\System\Database\SchemaForeignConstraintInfo;
use IGK\System\EntryClassResolution;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\IO\StringBuilder;
use IGKConstants;
use IGKEvents;
use IGKException;
use IGKType;
use ReflectionException;
use ReflectionMethod;

use function igk_resources_gets;
///<summary></summary>
/**
 * 
 * @package IGK\Helper
 */
class Database
{
    static $sm_shared_info;


    /**
     * 
     * @param mixed $model_class 
     * @return null|string 
     * @throws Error 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetPhpDocMacrosDefintionToInject($model_class): ?string
    {
        if (!($cl = self::GetMacroClass($model_class))) {
            return null;
        }
        return self::GetPhpDocMacrosDefintionToInjectFromMacroClass($cl);
    }
    
    public static function GetPhpDocMacrosDefintionToInjectFromMacroClass(string $macro_class, ?string $model_class=null):?string{

    
        $v_macro_class = $macro_class;

        $g = igk_sys_reflect_class($v_macro_class);
        $methods = $g->getMethods(ReflectionMethod::IS_PUBLIC || ReflectionMethod::IS_STATIC);
        usort($methods, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $sb = new StringBuilder;
        $s = '';

        foreach ($methods as $method) {
            $t = 'void ';
            $params = $method->getParameters();
            if (!($method->getNumberOfRequiredParameters() > 0))
                continue;
            if (!IGKType::ParameterIsTypeOf($params[0], ModelBase::class)) {
                continue;
            }
            array_shift($params);
            $ps = '';
            $ps = $params ? PhpHelper::GetParamerterDescription($params) : '';
            if ($method->hasReturnType()) {
                $v_return_type = $method->getReturnType();
                $tg = IGKType::GetName($v_return_type);
                if ($v_return_type->allowsNull()) {
                    $s = '?';
                }

                if ($model_class && ($model_class == $tg)){
                    $s .= 'static';
                }else {   
                    if (!IGKType::IsPrimaryType($tg)) {
                        $s .= '\\';
                    }
                }
                $s .= $tg;
                $t = $s . ' ';
            } 
            $sb->appendLine(sprintf("@method static %s%s(%s) macros function", $t, $method->getName(), $ps));
        }
        return $sb.'';
    }
    /**
     * 
     * @param string|\IGK\Models\ModelBase $model 
     * @return string 
     */
    public static function GetMacroClass($model): ?string
    {
        $instance = null;
        if (is_string($model) && is_subclass_of($model, ModelBase::class)) {
            $instance = $model::model();
        } else if ($model instanceof ModelBase) {
            $instance = $model;
        }
        if (is_null($instance)) {
            igk_die('failed to resolve model');
        }
        $s = null;
        $path = IGKConstants::NS_MACROS_CLASS . '\\' .
            ucfirst(basename(igk_uri(get_class($instance)))) . 'Macros';
        $s = $instance->getController()->resolveClass($path);

        return $s;
    }

    /**
     * get value from info
     * @param mixed $value 
     * @param mixed $name 
     * @param array<key,DbColumnInfo> $info 
     * @return mixed 
     */
    public static function GetValueFromLayoutInfo($value, $name,  $info = null)
    {
        if ($info) {
            $v_i = igk_getv($info, $name);
            if ($v_i) {
                if (!is_object($value) && self::IsNumber($v_i->clType)) {
                    return floatval($value);
                }
            }
        }
        return $value;
    }
    ///<summary></summary>
    ///<param name="t"></param>
    public static function IsNumber($t)
    {
        return preg_match("/(int|float|decimal|double|bigint|long)/i", $t);
    }
    private static function _Init()
    {
        self::$sm_shared_info = [];
    }
    /**
     * Get Shared info
     * @param string $n 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetInfo(string $n)
    {
        return igk_getv(self::$sm_shared_info, $n);
    }

    /**
     * init controller database 
     * @param BaseController $controller 
     * @return bool 
     */
    public static function InitData(BaseController $controller): bool
    {
        $controller->register_autoload();
        if (($cl = $controller->resolveClass(EntryClassResolution::DbInitData)) && class_exists($cl, false)) {
            $call = true;
            // + | Check Init :
            $c = new ReflectionMethod($cl, "Init");
            if ($type = $c->getParameters()[0]->getType()) {
                $call = IGKType::GetName($type) == get_class($controller);
            }
            // + | init data : 
            $recursive = false;
            if (isset($cl::$RecursiveInit)){
                $recursive = igk_getv(get_class_vars($cl), 'RecursiveInit');
            } 
            InitDataAnnotation::InitData($controller, $recursive); 
            
            $call && $cl::Init($controller);
            return true;
        }
        igk_ilog('missing init data - class : ' . $cl . ' ' . $controller);
        return false;
    }
    /**
     * clean table name by removing model table regex environment variables
     * @param string $table 
     * @param null|BaseController $controller 
     * @return string|string[]|null 
     */
    public static function GetCleanTableName(string $table, ?BaseController $controller = null)
    {
        $v = IGKConstants::MODEL_TABLE_REGEX;
        $t = preg_replace_callback(
            $v,
            function ($m) use ($controller) {
                switch ($m["name"]) {
                    case "prefix":
                        return '';
                    case "sysprefix":
                        return '';
                    case "year":
                        return date("Y");
                    case "date":
                        return date("Ymd");
                }
            },
            $table
        );
        return $t;
    }
    /**
     * init constants
     * @param string $constants 
     * @param mixed $model_or_class 
     * @param callable $c 
     * @return array<array-key, mixed>|false 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function InitConstansts(string $constants, $model_or_class, callable $c)
    {
        $ref = igk_sys_reflect_class($constants);
        if ($ref && $model_or_class && ($tab =  $ref->GetConstants())) {
            return array_map(new \IGK\Mapping\CreateModelIfNotExists($model_or_class, $c), $tab, array_keys($tab));
        }
        return false;
    }
    /**
     * for single value column 
     * @param string $constants 
     * @param mixed $model_or_class 
     * @param string $c column name  
     * @return array<array-key, mixed>|false 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function InitConstanstsColumn(string $constant_class, $model_or_class, string $c)
    {
        foreach ($constant_class::GetConstants() as $v) {
            $model_or_class::insertIfNotExists([
                $c => $v
            ]);
        }
    }
    public static function InitSystemDb(bool $force = false)
    {
        self::_Init();
        DBCaches::Reset($force);
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
    /**
     * 
     * @param BaseController $controller 
     * @param mixed $tb 
     * @param mixed $etb 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     */
    public static function CreateTableBase(BaseController $controller, $tb, $etb = null, $adapter = null)
    {
        $ctrl = $controller;
        $adapter = $adapter ?? $controller->getDataAdapter();
        if (is_null($adapter)) {
            igk_dev_wln_e("why is null " . $controller);
            return;
        }
        if (!$adapter->getIsConnect()) {
            return;
        }
        $dbname = $adapter->getDbName();
        $adapter->beginInitDb($ctrl);
        $v_foreignConstraints = [];
        foreach ($tb as $k => $v) {
            if (is_numeric($k)) {
                $k = $v->defTableName;
            }
            $n = igk_db_get_table_name($k, $ctrl);
            self::$sm_shared_info[$n] = $v;
            $data = $etb ? igk_getv($etb, $n) : null;
            igk_hook(IGK_NOTIFICATION_INITTABLE, [$ctrl, $n, &$data]);
            $columnInfo = $v->columnInfo;
            if ($dbname) {
                $n = sprintf('`%s`.%s', $dbname, $adapter->escape_table_name($n));
            }

            if (!$adapter->createTable($n, $columnInfo, $data, $v->description, $adapter->DbName)) {
                igk_push_env("db_init_schema", sprintf("failed to create  : %s", $n));
                igk_ilog("failed to create " . $n);
            } else {
                if ($v->foreignConstraint) {
                    $v_foreignConstraints[] = [$k, $v->foreignConstraint];
                }
            }
        }
        $adapter->endInitDb();
        if ($v_foreignConstraints) {
            array_map(function ($i) use ($adapter, $ctrl) {
                list($tbname, $a) = $i;
                $tbname = igk_db_get_table_name($tbname, $ctrl);
                $a->from = igk_db_get_table_name($a->from, $ctrl);
                $query = $adapter->getGrammar()->createAddConstraintReferenceForeignQuery($tbname, $a);
                if (!$adapter->sendQuery($query)) {
                    igk_ilog('failed to add reference : ' . $query);
                }
            }, $v_foreignConstraints);
        }
        igk_hook(IGKEvents::HOOK_DB_POST_GROUP, [
            'ctrl' => $controller
        ]);
    }

    /**
     * only for system an core
     * @param BaseController $controller 
     * @param array $definition table definition 
     * @param bool $force force to init logic
     * @return void 
     */
    public static function InitDbCoreLogic(BaseController $controller, $definitions, bool $force)
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
    public static function InitDataEntries(BaseController $controller)
    {
        // check if controller can process 
        $adapter = $controller->getDataAdapter();
        if (is_null($adapter)) {
            igk_dev_wln_e(__FILE__ . ":" . __LINE__, "adpter is null " . $controller);
        }
        if ($adapter && !$adapter->canProcess()) {
            return;
        }
        //initialise manager 
        // + | --------------------------------------------------------------------
        // + | BEFORE INIT - APPLICATION
        // + |
        if (!(($cl = $controller->resolveClass(EntryClassResolution::DbInitManager)) && class_exists($cl, false)
            && is_subclass_of($cl, \IGK\Database\DbInitManager::class))) {
            $cl = \IGK\Database\DbInitManager::class;
        }
        if ($cl) {
            (new $cl($controller))->init($controller);
        }
        if (($cl = $controller->resolveClass(EntryClassResolution::DbInitData)) && class_exists($cl, false)) {
            $call = true;
            // + | Check Init 
            $c = new ReflectionMethod($cl, "Init");
            if ($type = $c->getParameters()[0]->getType()) {
                $call = IGKType::GetName($type) == get_class($controller);
            }
            $call && $cl::Init($controller);
        }
    }

    /**
     * 
     * @param BaseController $controller 
     * @return void 
     */
    public static function DropForeignKeys(BaseController $controller)
    {
        $tableinfo = $controller->getDataTableInfo();
        $tables = igk_getv($tableinfo, 'tables');
        if ($tables) {
            $keys = array_keys($tables);
            $controller->getDataAdapter()->dropForeignKeys($keys);
            return true;
        }
        return false;
    }
    public static function DropUniquesContraints(BaseController $controller)
    {
        $tableinfo = $controller->getDataTableInfo();
        if ($tableinfo->tables) {
            $keys = array_keys($tableinfo->tables);
            $controller->getDataAdapter()->dropForeignKeys($keys, 1);
            return true;
        }
        return false;
    }
    /**
     * drop table from regex
     * @param BaseController $ctrl 
     * @param string $regex 
     * @return array|null 
     * @throws IGKException 
     */
    public static function DropTableFromRegex(BaseController $ctrl, string $regex)
    {
        $db = igk_get_data_adapter($ctrl, true);
        if ($db->connect()) {
            $r = $db->listTables();
            if (!$r) {
                $db->close();
                return null;
            }
            $n = igk_getv(array_keys((array)$r[0]), 0);
            $tab = array();
            foreach ($r as $v) {
                if (preg_match($regex, $v->$n)) {
                    $tab[] = $v->$n;
                }
            }
            $db->dropTable($tab);
            $db->close();
            return ['tables' => $tab];
        }
        return null;
    }

    /**
     * auto prefix column management 
     * @param string $column 
     * @param null|string $prefix 
     * @return string 
     */
    public static function AutoPrefixColumn(string $column, ?string $prefix=null){
        if(empty($prefix)){
            return $column;
        }
        if (!igk_str_startwith($column, $prefix)){
            $column = $prefix.$column;
        }
        return $column;
    }
}
