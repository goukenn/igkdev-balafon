<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbSchemaDefinitions.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Database;
 
use Exception;
use IGK\Controllers\BaseController; 
use IGK\System\Caches\DBCaches; 
use IGKException;

abstract class DbSchemaDefinitions
{
    /**
     * store schema definition [DRIVER][CTRL][TABLE INFO]
     * @var mixed
     */
    private static $sm_def;
    private static $sm_init = false;
    private static $sm_init_cache = false;
    const CACHE_FILE = ".data-schema.definition.cache";
    const RESOLV_KEY = 'resolved';
    /**
     * get of copy of stored definitions scheam
     * @return mixed 
     */
    public static function GetDefinitions()
    {
        return self::$sm_def;
    }
    private static function _InitDefinition(
        $info,
        $controller,
        string $v_resolveKey,
        ?string $table = null,
        array $seriedata = null
    ) {
        $cl = get_class($controller);
        $adname = $controller->getDataAdapterName();
        $_key = null;
        if ($table){
            $_key = self::_GetResolKey($adname, $table);
            self::_BindInfoTable($info, $v_resolveKey, $_key, $cl);
        }
        // self::$sm_def[$v_resolveKey][$_key] = [$info, $cl];
        if ($seriedata && isset($seriedata[$adname][$cl])) {
            $b_key = $_key;
            $other = $seriedata[$adname][$cl]->tables;
            foreach ($other as $k => $v) {

                $_key = self::_GetResolKey($adname, $k);
                if ($b_key == $_key)
                    continue;
                self::_BindInfoTable($v, $v_resolveKey, $_key, $cl);
            }
        }
        self::$sm_def[$adname] = [];
    }
    //schema definition ADAPTER/CONTROLLER TABLE INFO -- 
    /**
     * get table definition 
     * @param string $ad_name 
     * @param string $table 
     * @return mixed 
     */
    public static function GetDataTableDefinition(string $ad_name, string $table)
    {

        $c = (object)DBCaches::GetTableInfo($table, null);

        return $c;

        // $v_resolveKey = self::RESOLV_KEY;
        // self::_InitFromCache($v_resolveKey);
        // $key = self::_GetResolKey($ad_name, $table);
        // if ($info = igk_getv(self::$sm_def[$v_resolveKey], $key)) {
        //     return $info[0];
        // }
        // $result = null;
        // if (isset(self::$sm_def[$ad_name])) {
        //     $result = self::_GetInfoTable($ad_name, $table,  $v_resolveKey, $key);
        // } else {
        //     // 
        //     if (!self::$sm_init) {
        //         $fc = Closure::fromCallable([self::class, '_InitDefinition']);
        //         $loader = EnvControllerCacheDataBase::Init($fc, $ad_name, $table, $v_resolveKey);
               
        //         if ($info = igk_getv(self::$sm_def[$v_resolveKey], $key)) {
        //             $result =  $info[0];
        //         }else if (isset(self::$sm_def[$ad_name])) {
        //             $result = self::_GetInfoTable($ad_name, $table,  $v_resolveKey, $key);
        //         }
        //     }
        // }
        // self::$sm_init = true;
        // return $result;
    }
    private static function _GetInfoTable(string $ad_name, string $table, $v_resolveKey, $key)
    {
        $d = self::$sm_def[$ad_name];
        $result = null;
        foreach ($d as $ctrl => $info) {
            if ($inftable = igk_getv($info->tables, $table)) {
                // init resolved table definition - 
                $result = self::_BindInfoTable($inftable, $v_resolveKey, $key, $ctrl);
                break;
            }
        }
        return $result;
    }
    private static function _BindInfoTable($inftable, $v_resolveKey, $key, $ctrl)
    {
        // + | --------------------------------------------------------------------------
        // + | detect the presence of tableRowReference to convert used data to IDbColumn
        // + | so we can said that it comming from serialize data. ----------------------        
        
        if (!isset($inftable->tableRowReference)) {
            $inftable->columnInfo = array_map([self::class, 'ToColumnInfoDefinition'], (array) $inftable->columnInfo);
            $inftable->tableRowReference = igk_array_object_refkey($inftable->columnInfo, IGK_FD_NAME);
        }
        self::$sm_def[$v_resolveKey][$key] = [
            $inftable, $ctrl
        ];
        $inftable->context = 'binding_info';
        $inftable->controller = $ctrl;
        return $inftable;
    }
    private static function _InitFromCache($v_resolveKey)
    { 
        // $i = 0;
        // if (is_null(self::$sm_def) || !self::$sm_init_cache) {
        //     // + | --------------------------------------------------------------------
        //     // + | LOAD FROM DB CACHE
        //     // + |             
        //     if (is_file($file = igk_io_cachedir() . "/" . self::CACHE_FILE)) {
        //         $s = file_get_contents($file);
        //         if (!empty($s)) {
        //             if (($g = unserialize($s)) === false) {
        //                 @unlink($s);
        //             } else {
        //                 self::$sm_def = $g;
        //                 $i = 1;
        //             }
        //         }
        //     }
        //     !$i && (self::$sm_def = []);
        //     self::$sm_def[$v_resolveKey] = [];
        //     self::$sm_init_cache = true;
        // }
    }
    private static function _GetResolKey($ad_name, $table)
    {
        return sha1($ad_name . ":" . $table);
    }
    static function ToColumnInfoDefinition($m)
    {
        return new DbColumnInfo((array)$m);
    }
    /**
     * register schema definition 
     * @param mixed $ad_name 
     * @param mixed $table 
     * @param mixed $info 
     * @return void 
     */
    public static function RegisterDataTableDefinition($ad_name, $table, &$info)
    {
        if (!isset(self::$sm_def[$ad_name])) {
            self::$sm_def[$ad_name] = [];
        }
        self::$sm_def[$ad_name][$table] = &$info;
    }
    /**
     * remove all from cache
     * @param BaseController $controller 
     * @return bool 
     */
    public static function UnregisterCache(BaseController $controller)
    {
        self::_InitFromCache(self::RESOLV_KEY);
        $ad_name = $controller->getDataAdapterName();
        if ($p = igk_getv(self::$sm_def, $ad_name)) {
            unset($p[get_class($controller)]);
            self::$sm_def[$ad_name] = $p;
        }
        return true;
    }

    /**
     * lead definition 
     * @param BaseController $controller 
     * @param bool $store 
     * @return void 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function Reload(BaseController $controller, $store = true)
    {
        //$ad_name = $controller->getDataAdapterName();
        $controller->getDataTableDefinition();
        // $store && EnvControllerCacheDataBase::StoreCache();
    }
    public static function UpdateTableDefition(BaseController $controller, $def, $store = true)
    {
       // $store && EnvControllerCacheDataBase::UpdateCache($controller, $def);
    }
    /**
     * reset db definition cache
     * @return void 
     */
    public static function ResetCache($resetDbCache = true){
        self::$sm_def = [];
        self::$sm_init = false;
        self::$sm_init_cache = false;
        $resetDbCache && DbSchemas::ResetSchema();
        $resetDbCache && EnvControllerCacheDataBase::ResetCache();
    }
}
