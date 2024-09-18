<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbUtils.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\RootControllerBase;
use IGK\Controllers\SysDbController;
use IGK\Database\IDbColumnInfo;
use IGK\Models\ModelBase;
use IGK\System\Caches\DBCaches;
use IGK\System\Caches\DBCachesModelInitializer;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKException;
use ReflectionClass;
use ReflectionException;

class DbUtils
{

    /**
     * order controller callback
     */
    const OrderController = self::class . "::OrderController";

    /**
     * get dump files 
     * @param array $table_info 
     * @return int[] 
     */
    public static function GetDumpFields(array $table_info){
        $g = [];
        foreach($table_info as $k=>$info){
            if ($info->getIsDumpField()){
                $g[$k]=1;
            }
        }
        return $g;
    }
    /**
     * 
     * @param IDbColumnInfo $column_info 
     * @param mixed $value 
     * @return bool 
     */
    public static function GetIsDumpField(IDbColumnInfo $column_info):bool{
        $c = $column_info;
        if (is_null($c->clIsDumpField)){
            // auto determine if c column info is a dump field
            $d = strtolower($c->clType); 

            return !(($c->clAutoIncrement) || 
                (($d=='datetime') && $c->clInsertFunction ||(strtolower($c->clDefault.'') == 'now()')) ||
                (($d=='varchar') && ($c->clInsertFunction == 'IGK_PASSWD_ENCRYPT')));
        }
        return $c->clIsDumpField;
    }
    public static function OrderController($a, $b)
    {
        if (get_class($a) == \IGK\Controllers\SysDbController::class) {
            return -1;
        }
        if (get_class($b) == \IGK\Controllers\SysDbController::class) {
            return -1;
        }
        if (RootControllerBase::IsSystemController($a)) {
            if (RootControllerBase::IsSystemController($b)) {
                return strcmp(get_class($a), get_class($b));
            }
        }
        return 1;
    }
    public static function ResolvDefTableTypeName(string $table, BaseController $controller)
    {
        $sys_prefix = igk_configs()->db_prefix;
        $prefix =
            ($controller instanceof SysDbController) ? $sys_prefix :
            $controller->getConfig('clDataTablePrefix', $sys_prefix);
        if ($prefix && (strpos($table, $prefix) === 0)) {
            $table = '%prefix%' . substr($table, strlen($prefix));
        }
        return $table;
    }
    /**
     * reset string controller database
     * @param BaseController $controller 
     * @return bool 
     * @throws IGKException 
     */
    public static function ResetControllerDb(BaseController $controller)
    {
        if (!$controller::resetDb(false, true)) {
            return false;
        }
        $defs =  DBCaches::Update($controller);
        if ($defs) {
            DBCachesModelInitializer::Init($defs->tables, true);            
        }
        return true;
    }
    /**
     * drop controller tables
     * @param BaseController $controller 
     * @return true|void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public static function DropControllerTables(BaseController $controller)
    {
        $def = DBCaches::Update($controller) ?? igk_die('no definition found');
        $tables = $def->tables;
        $ad = $controller::getDataAdapter();
        if ((count($tables)>0) && $ad && $ad->connect()) {
            $ad->setForeignKeyCheck(false);
            foreach (array_keys($tables) as $table) {
                $ad->dropTable($table);
            }
            $ad->setForeignKeyCheck(true);
            $ad->close();
            return true;
        }
    }

    /**
     * only column regex filter 
     * @param mixed $column 
     * @return string 
     */
    public static function OnlyColumnFilterRegex($column){
        if (is_array($column)){
            $column = implode("|", $column);
        }
        return sprintf("/\b(?!%s)\b[\w][\w\d_]*\b/i", $column);
    }

    /**
     * get columns columns
     * @param ModelBase $model 
     * @param mixed ...$columns_list 
     * @return array 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function ModelColumns(ModelBase $model, ...$columns_list){
        // $consts = igk_sys_reflect_class ($model)->getConstants();
        $tm = $columns_list;
        $tp = [];
        $consts = self::GetDeclaredColumnConstants($model);
        // + | to preserve declaration order
        $tl = array_combine(array_values($consts), array_keys($consts));
        foreach($tm as $k){
            if (key_exists($k, $tl)){
                $tp[$tl[$k]] = $k;
            }
        }
        // foreach ($consts as $ck=>$cv){
        //     if (preg_match("/^FD_/",$ck) && !isset($tb[$ck])){
        //         if (in_array($cv, $tm)){
        //             $tp[$ck] = $cv;
        //         }
        //     }
        // }
        return $tp;
    }
    public static function GetDeclaredColumnConstants(ModelBase $model){
        $consts = igk_sys_reflect_class ($model)->getConstants();
        $l = [];
        foreach ($consts as $ck=>$cv){
            if (preg_match("/^FD_/",$ck)){
                $l[$ck]= $cv;
            }
        }
        return $l;

    }

    /**
     * 
     * @param IDbColumnInfo $column_info 
     * @return bool 
     */
    public static function IsJoinTableLinkCandidate($column_info):bool{
        
        return (
            $column_info->clIsIndex || $column_info->clIsPrimary || $column_info->clAutoIncrement
        ); 
    }
}
