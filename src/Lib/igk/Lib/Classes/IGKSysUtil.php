<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKSysUtil.php
// @date: 20220803 13:48:54
// @desc: 

///<summary> System utility class </summary>

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Controllers\SysDbControllerManager;
use IGK\Database\DbColumnInfoPropertyConstants;
use IGK\Database\SQLQueryUtils;
use IGK\Helper\ArrayUtils;
use IGK\Helper\Database;
use IGK\Helper\IO;
use IGK\Helper\StringUtility as IGKString;
use IGK\System\Caches\DBCaches;
use IGK\System\Database\DbUtils;
use IGK\System\EntryClassResolution;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\Test\IGKValueEntryCallbackTest;

/**
 * 
 * @package 
 */
abstract class IGKSysUtil
{
    const PRIMARY_PWD = '#_12549@abcdkqors';
    private function __construct()
    {
    }
    /**
     * shuffle password files
     * @return string 
     */
    public static function GeneratePWD(){
        return substr(str_shuffle(self::PRIMARY_PWD), 0, 9);
    }

    /**
     * get model full type name 
     * @param string $table table name to get 
     * @param ?BaseController $controller controller in use to the model type name
     * @return string 
     */
    public static function GetModelTypeName(string $t, ?BaseController $ctrl = null): string
    {
        $_NS = "";
        $t = preg_replace(IGKConstants::MODEL_TABLE_REGEX, "", $t);
        if ($ctrl) {
            $_NS = $ctrl::ns(EntryClassResolution::Models) . "\\";
        }
        $name = preg_replace("/\\s/", "_", $t);
        $name = implode("", array_map("ucfirst", array_filter(explode("_", $name))));
        return $_NS . $name;
    }
    /**
     * return model type name
     * @param mixed $tableinfo with defTableName key property
     * @return null|string 
     * @throws IGKException 
     */
    public static function GetModelTypeNameFromInfo($tableinfo, & $table = null) :?string{
        $table = igk_getv($tableinfo, DbColumnInfoPropertyConstants::DefTableName);
        if (!empty($table)) {
            return basename(igk_uri(self::GetModelTypeName($table)));
        }
        return null; 
    }
    public static function Encrypt($data, $prefix = null)
    {
        if ($prefix === null) {
            $prefix = defined("IGK_PWD_PREFIX") ? IGK_PWD_PREFIX : "";
        }
        return hash("sha256", $prefix . $data);
    }
    /**
     * clear lib controller
     * @return void 
     */
    public static function CleanLibFolder()
    {
        if ($hdir = opendir($rdir = realpath(IGK_LIB_DIR . "/../"))) {
            while ($cdir = readdir($hdir)) {
                if (($cdir == ".") || ($cdir == "..") || ($cdir == "igk")) {
                    continue;
                }
                if (is_dir($b = $rdir . "/" . $cdir)) {
                    IO::RmDir($b);
                }
            }
        }
    }
    /**
     * Retreive all managed data information
     * @param string $dataadapter 
     * @return array 
     * @throws Exception 
     */
    public static function GetConfigDataInfo($dataadapter = IGK_MYSQL_DATAADAPTER)
    {
        $ctrl = igk_app()->getControllerManager()->getControllers();
        $tables = [];
        foreach ($ctrl as $v) {
            $tables = array_merge($tables, self::GetControllerConfigDataInfo($v, $dataadapter));
        }
        return $tables;
    }
    /**
     * 
     * @param mixed $file 
     * @return void 
     */
    public static function GetDataDefinitionFromFile($file, $v = null, &$tables = null)
    {
        if ($tables === null)
            $tables = [];
        $data = igk_db_load_data_schemas($v->getDataSchemaFile());
        $tschema = $data->tables;
        if ($tschema) {
            $entries = [];
            foreach ($tschema as $ck => $cv) {
                if (isset($tables[$ck])) {
                    igk_ilog("Table $ck already found. [" . $v->Name . "] get from " . $tables[$ck]->ctrl->Name . " with schema");
                    return null;
                }

                $cinfo = $cv->columnInfo;
                $desc = $cv->description;
                $entries = $cv->entries;              
                $tables[$ck] = (object)array("info" => $cinfo, "ctrl" => $v, "desc" => $desc, "entries" => $entries);
            }
        }
        return $tables;
    }
    /**
     * retriceve all data info form controller
     * @param mixed $controller 
     * @param string $dataadapter 
     * @return null|array 
     * @throws Exception 
     */
    public static function GetControllerConfigDataInfo($controller, $dataadapter = IGK_MYSQL_DATAADAPTER)
    {
        $tables = [];
        $v = $controller;
        if ($v->getDataAdapterName() == $dataadapter) {
            $b = BaseController::Invoke($v, "getUseDataSchema");
            // 
            if (!$b) {
                $tname = $v->getDataTableName();
                $tinfo = $v->getDataTableInfo();
                if (!empty($tname) && ($tinfo !== null)) {
                    if (isset($tables[$tname])) {
                        igk_ilog("Table $tname already found. [" . $v->Name . "] get from " . $tables[$tname]->ctrl->Name . " no schema");
                        return null;
                    }
                    $tables[$tname] = (object)array("info" => $tinfo, "ctrl" => $v, "desc" => null, "entries" => null);
                }
            } else {
                self::GetDataDefinitionFromFile($v->getDataSchemaFile(), $v, $tables);
            }
        }
        return $tables;
    }

    /**
     * 
     * @param BaseController $controller 
     * @return string 
     */
    public static function GetControllerSqlQueryData(BaseController $controller): string
    {
        $s = "";
        if ($tables = self::GetControllerConfigDataInfo($controller)) {
            $controller->Db->connect();
            $relations = [];
            $tentries = [];
            $grammar = $controller->getDataAdapter()->getGrammar();
            foreach ($tables as $table => $info) {
                //     Utils::GenerateAndWriteMigration($table, $info, $out) || die("failed to write ".$table);


                $s .=  $grammar->CreateTableQuery($table, $info->info, $info->desc) . PHP_EOL;
                $refered = 0;
                $refered_counter = 0;
                $links = "";
                $queryfilter = igk_environment()->mysql_query_filter;
                foreach ($info->info as $ti) {


                    if ($ti->clLinkType) {
                        $refColumn = igk_getv($ti, "clLinkColumn", IGK_FD_ID);
                        $nk = $queryfilter ? '' :
                            igk_getv($ti, "clLinkConstraintName", '`' . $table . "_" . $ti->clName . '`');

                        $links .= trim(IGKString::Format(
                            "ALTER TABLE {0} ADD CONSTRAINT {1} FOREIGN KEY (`{2}`) REFERENCES {3}  ON DELETE RESTRICT ON UPDATE RESTRICT;",
                            "`{$table}`",
                            $nk,
                            $ti->clName,
                            IGKString::Format(
                                "`{0}`(`{1}`)",
                                $ti->clLinkType,
                                $refColumn
                            )
                        ));

                        if ($refered = ($refered || ($ti->clLinkType != $table))) {
                            $refered_counter++;
                        }
                    }
                }
                if ($entry = $info->entries) {
                    if (!isset($tentries[$refered_counter])) {
                        $tentries[$refered_counter] = [];
                    }
                    array_push($tentries[$refered_counter], ["table" => $table, "entries" => $entry]);
                }
                if (!empty($links)) {
                    if (!isset($relations[$refered_counter])) {
                        $relations[$refered_counter] = "";
                    } else {
                        $relations[$refered_counter] .=  PHP_EOL;
                    }
                    $relations[$refered_counter] .= $links;
                }
            }
            if (!empty($relations)) {
                krsort($relations);
                foreach ($relations as $v) {
                    $s .= trim($v) . PHP_EOL;
                }
                //$s .= implode(PHP_EOL, $relations); 
            }
            if ($tentries) {
                krsort($tentries);
                foreach ($tentries as $te) {
                    foreach ($te as $m) {
                        $tb = $m["table"];
                        foreach ($m["entries"] as $c) {
                            $s .= SQLQueryUtils::GetInsertQuery($tb, $c) . PHP_EOL;
                        }
                    }
                }
            }
            $controller->Db->close();
        }
        return $s;
    }

    /**     
     * resolv the table name
     *
     * @param string $table table or column name to resolv
     * @param null|BaseController $ctrl 
     * @return string|string[]|null 
     */
    public static function DBGetTableName(string $table, ?BaseController $ctrl = null)
    { 
        $v = IGKConstants::MODEL_TABLE_REGEX;
        $t = preg_replace_callback(
            $v,
            function ($m) use ($ctrl) {
                $p = igk_app()->configs->get("db_prefix");
                switch ($m["name"]) {
                    case "prefix":
                        if ($ctrl) {
                            if (!empty($s = $ctrl->getConfigs()->clDataTablePrefix)) {
                                $p = $s;
                            }
                        }
                        return $p;
                    case "sysprefix":
                        return $p;
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
     * 
     * @param array $inf 
     * @param BaseController $ctrl 
     * @return array 
     */
    public static function DBGetPhpDocModelArgEntries(array $inf, BaseController $ctrl)
    {
        $tab = [];
        $require = [];
        $optional = [];
        $skeys = [];
        foreach ($inf as $column => $prop) {
            if (is_integer($column))
            {
                $column = $prop->clName;
            }
            $skeys[$column] = $prop;

            if ($prop->clAutoIncrement) {
                continue;
            }
            if ($prop->clDefault) {
                $optional[] = $column;
                continue;
            }
            $require[] = $column;
            
        }
        $tab = array_merge($require, $optional);
        $tab = array_combine($tab, $tab); 

        $g = array_map(function ($i) use ($ctrl, $skeys) {
            return self::GetPhpDoPropertyType($i, $skeys[$i], $ctrl, true);
        }, $tab);
        return $g;
    }
    /**
     * 
     * @param mixed $name 
     * @param mixed $info 
     * @param BaseController $ctrl 
     * @param bool $extra 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetPhpDoPropertyType($name, $info, BaseController $ctrl, $extra = false)
    { 
        $t = self::ConvertToPhpDocType($info->clType);
        if ($info->clLinkType) {
            $t = "int|" . self::GetLinkType($info->clLinkType, $info->clNotNull, $ctrl);
        }
        $extra = "";
        if ($info->clDefault) {
            $extra .= " =\"" . $info->clDefault . "\"";
        }
        // + | --------------------------------------------------------------------
        // + | comment 
        // + | 
        return $t . " \$" . $name . $extra;
    }

    /**
     * reverse table table t
     * @param string $table 
     * @param null|BaseController $ctrl 
     * @return string 
     */
    public static function DBReverseTableName(string $table, ?BaseController $ctrl = null)
    {
        $c = $table;
        foreach (["%prefix%", "%sysprefix%", "%year%"] as $v) {
            if ($vv = self::DBGetTableName($v, $ctrl)) {
                $c = str_replace($vv, $v, $c);
            }
        }
        return $c;
    }
    /**
     * Get Link type helper
     * @param mixed $type 
     * @param bool $notnull 
     * @param null|BaseController $ctrl 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetLinkType($type, ?bool $notnull, ?BaseController $ctrl = null)
    {
        $t = "";
        if (!$notnull) {
            $t .= "?";
        }
        $t .= "\\";
        $g = &\IGK\Models\ModelBase::RegisterModels();

        if (isset($g[$type])) {
            if (!isset($g[$type]->model)) {
                igk_die(" model class defined.");
            }
            $t .= $g[$type]->model;
        } else {
            // retrieve model 
            $list = [];
            if ($ctrl){
                $list[] = $ctrl;
                $type = igk_db_get_table_name($type, $ctrl);
            }
            if (SysDbController::ctrl() !== $ctrl) {
                $list[] = SysDbController::ctrl();
            }
            while ($q = array_shift($list)) {
                // if ($gu = SysDbControllerManager::GetDataTableDefinitionFormController($q, $type)) {
                if ($gu = DBCaches::GetTableInfo($type, $q)) {
                    break;
                }
            }
            if (is_null($gu)) {
                // $gm = Database::GetInfo($type);
                $g = DBCaches::GetColumnInfo($type); 
                if (is_null($g)){
                    igk_die(sprintf("dadata base do not retrieve [%s] data table info.", $type));
                } else {
                     # build - schema migration info

                }
            }
            if (!isset($gu->modelClass)) {
                if (!isset($gu->defTableName)){
                    $gu->defTableName = DbUtils::ResolvDefTableTypeName($type, $ctrl);  
                } 
                $gu->modelClass = IGKSysUtil::GetModelTypeName($gu->defTableName, $ctrl);
            }
            $t .=  $gu->modelClass;
        }
        return $t;
    }
    public static function ConvertToPhpDocType($type)
    {
        if (is_null($type)){
            return 'string';
        }
        return igk_getv([
            "varchar" => "string",
            "int" => "int",
            "decimal" => "int",
            "float" => "float",
            "datetime" => "string|datetime"
        ], strtolower($type), "string");
    }
}
