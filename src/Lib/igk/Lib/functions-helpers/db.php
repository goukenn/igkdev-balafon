<?php

// @author: C.A.D. BONDJE DOUE
// @filename: db.php
// @date: 20230202 17:23:52
// @desc: db function helper

///<summary></summary>
///<param name="controllerOrAdapterName"></param>
///<param name="table"></param>
///<param name="dbname" default="null"></param>
///<param name="leaveOpen" default="false"></param>

use IGK\Controllers\BaseController;
use IGK\Database\DbColumnInfo;
use IGK\Database\DbSchemas;
use IGK\Models\Groupauthorizations;
use IGK\System\Console\Commands\Database\DbSchemaUtility;
use IGK\System\Console\Logger as Logger;

if (!function_exists('igk_db_table_info')) {

    /**
     * operate on table information with a callback 
     * @param BaseController $ctrl 
     * @param callable $callback 
     * @param bool $connect do not require Adapter connection  
     * @return void 
     * @throws IGKException 
     */
    function igk_db_table_info(BaseController $ctrl, callable $callback, bool $connect = true)
    {
        $db = $ctrl->getDataAdapter();
        if (!$ctrl->getUseDataSchema()) {
            if (
                !empty($table = $ctrl->getDataTableName()) &&
                ($info = $ctrl->getDataTableInfo())
                && ($connect || ($db && $db->connect()))
            ) {
                $table = igk_db_get_table_name($table, $ctrl);
                $callback($table, $info);
                $connect || $db->close();
            }
        } else {
            $tb = $ctrl::loadDataFromSchemas();
            if ($connect || ($db && $db->connect())) {
                $v_tblist = [];
                if ($tables = igk_getv($tb, "tables")) {
                    foreach (array_keys($tables) as $k) {
                        $v_tblist[$k] = $k;
                        $callback($k, $tables[$k]);
                    }
                }
                $connect || $db->close();
            }
        }
    }
}


/**
 * helper: reload with index .
 * @param mixed $controllerOrAdapterName 
 * @param mixed $table 
 * @param mixed $dbname 
 * @param mixed $leaveOpen 
 *  
 */
function igk_db_reload_index($controllerOrAdapterName, $table, $dbname = null, $leaveOpen = false)
{
    $adapt = igk_get_data_adapter($controllerOrAdapterName, false);
    if ($adapt) {
        $adapt->connect($dbname);
        $r = $adapt->selectAll($table);
        $adapt->ClearTable($table);
        $i = 1;
        foreach ($r->getRows() as $v) {
            $v->clId = $i;
            $adapt->insert($table, $v);
            $i++;
        }
        $adapt->close($leaveOpen);
        return $r;
    }
    return null;
}

/**
 * @deprecated use direct IGK\Helper\AutorizationHelper
 */
function igk_db_is_user_authorized($s, $actionName, $strict = false, $authTable = IGK_TB_AUTHORISATIONS, $userGroupTable = IGK_TB_USERGROUPS, $userGroupAuthTable = IGK_TB_GROUPAUTHS)
{
    if (!is_object($s) || empty($actionName))
        return 0;
    $authTable = igk_db_get_table_name($authTable);
    $userGroupTable = igk_db_get_table_name($userGroupTable);
    $userGroupAuthTable = igk_db_get_table_name($userGroupAuthTable);
    $r = igk_db_table_select_row($authTable, array(IGK_FD_NAME => $actionName));
    $v_r = false;
    if ($r) {
        $v_authid = $r->clId;
        $v_usergroup = igk_db_table_select_where($userGroupTable, array(IGK_FD_USER_ID => $s->clId));
        if ($v_usergroup->RowCount <= 0)
            return $v_r;
        foreach ($v_usergroup->Rows as $item) {
            $q = igk_db_table_select_where($userGroupAuthTable, array(
                IGK_FD_GROUP_ID => $item->clGroup_Id,
                IGK_FD_AUTH_ID => $v_authid
            ));
            if ($q && ($q->RowCount == 1)) {
                $grant = $q->getRowAtIndex(0)->clGrant;
                if (!$v_r) {
                    if ($grant != 1) {
                        if ($strict)
                            return false;
                    }
                    $v_r = true;
                } else {
                    if ($grant != 1) {
                        return false;
                    }
                }
            } else {
                if (!$strict) {
                    continue;
                }
                break;
            }
        }
        return $v_r;
    } else {
        $p = (object)array();
        $p->clName = $actionName;
        $ctrl = igk_db_get_datatableowner($authTable);
        if ($ctrl) {
            $ctrl->model()->insert($authTable, $p);
        }
        // igk_db_insert($ctrl, $authTable, $p);
    }
    return $v_r;
}

///<summary>insert in table if data not exists</summary>
/**
 * insert in table if data not exists
 * @deprecated use direct model access
 */
function igk_db_insert_if_not_exists($controllerOrAdpaterName, $table, $entry, $condition = null, $dbname = null, $leaveOpen = false, $Op = 'OR')
{
    $adapt = igk_get_data_adapter($controllerOrAdpaterName, false);
    $v_is_c = igk_is_controller($controllerOrAdpaterName);
    $table = igk_db_get_table_name($table, $v_is_c ? $controllerOrAdpaterName : null);
    $r = false;
    if ($adapt) {
        $tabinfo = DbSchemas::GetTableColumnInfo($table);
        if ($tabinfo == null) {
            if (!igk_sys_env_production()) {
                if ($v_is_c) {
                    $b = igk_db_ctrl_datatable_info_key($controllerOrAdpaterName, $table);
                }
                igk_wln(igk_db_ctrl_datatable_info_key($controllerOrAdpaterName, $table));
                igk_die("/tab info is null :  " . $table);
            }
            return -1;
        }
        $c = $v_is_c ? $controllerOrAdpaterName : $dbname;
        if ($adapt->connect()) {
            $e = null;
            if (igk_count($tabinfo) == 0) {
                igk_debug_wln(__FUNCTION__ . ":::table [" . $table . "]");
            }
            if ($condition == null) {
                $e = igk_db_data_is_present($adapt, $table, $entry, $tabinfo);
            } else {
                $e = igk_db_data_is_present($adapt, $table, $condition, $tabinfo);
            }
            if (!$e) {
                $r = $adapt->insert($table, $entry, false); // , $tabinfo);
            }
            $adapt->close($leaveOpen);
        } else {
            igk_ilog("failed connect");
        }
        return $r;
    } else {
        igk_ilog("/!\\ Adapter is null or connected ...." . $table);
    }
    return null;
}

///<summary></summary>
///<param name="ctrl"></param>
///<param name="entries"></param>
/**
 * utility global function 
 * @param BaseController $ctrl 
 * @param array $entries 
 * @deprecated use direct Model access
 */
function igk_db_insertc(BaseController $ctrl, $entries)
{
    $tb = $ctrl->getDataTableName();
    $ad = $ctrl->getDataAdapter();
    return $tb && $ad && $ad->insert($tb, $entries);
    // return igk_db_insert($ctrl, $ctrl->getDataTableName(), $entries, null);
}

///<summary> shortcut to add multiple entries on the table</summary>
///<param name="strict"> add stop if error dected</param>
/**
 *  shortcut to add multiple entries on the table
 * @param mixed $strict  add stop if error dected
 */
function igk_db_inserts($ad, $table, $entries, $strict = 1)
{
    $error = 1;
    foreach ($entries as $v) {
        $error = $ad->insert($table, $v) && $error;
        if (!$error && $strict) {
            break;
        }
    }
    return !$error;
}


///<summary></summary>
///<remark>if entrie is a std class the reponds will have a clid updated </remark>
/**
 * @deprecated use model entry class
 */
function igk_db_insert($controllerOrAdpaterName, $table, $entries, $dbname = null, $leaveOpen = false)
{
    $adapt = igk_get_data_adapter($controllerOrAdpaterName, false);
    if ($adapt) {
        if ($adapt->connect($dbname)) {
            $r = $adapt->insert($table, $entries, false);
            if (!$r) {
                igk_ilog("sql error : " . igk_mysql_db_error());
            }
            $adapt->close($leaveOpen);
            return $r;
        }
    } else {
        igk_db_error("Adapter is null");
    }
    return null;
}

///<summary></summary>
///<summary></summary>
///<param name="ctrl"></param>
///<param name="leaveopen" default="false"></param>
/**
 * 
 * @param mixed $ctrl 
 * @param mixed $leaveopen 
 */
function igk_db_last_id($ctrl, $leaveopen = false)
{
    $db = igk_get_data_adapter($ctrl);
    $r = null;
    if ($db) {
        if ($db->connect()) {
            $r = $db->last_id();
            $db->close($leaveopen);
        } else {
            igk_debug_wln("<div class=\"igk-danger\" >/!\ connect to db failed [" . __FUNCTION__ . "]</div>");
        }
        return $r;
    }
    return null;
}

///<summary></summary>
///<param name="groups"></param>
/**
 * 
 * @param mixed $groups 
 */
function igk_db_init_groups($groups)
{
    foreach ($groups as $v) {
        igk_db_register_group($v);
    }
}


///invoke to init system auto
/**
 * @deprecated
 */
function igk_db_init_auths($auths)
{
    $c = igk_db_get_datatableowner(igk_db_get_table_name(IGK_TB_AUTHORISATIONS));
    foreach ($auths as $v) {
        igk_db_register_auth($v, $c);
    }
}


///<summary>grant system authorisation</summary>
///<exemple>igk_db_grant('sys://drop', 'root')</exemple>
/**
 * grant system authorisation
 */
function igk_db_grant($authname, $groupname, $access = 1, $ctrl = null)
{
    $auth = igk_db_table_select_row(igk_db_get_table_name(IGK_TB_AUTHORISATIONS), array(IGK_FD_NAME => $authname));
    $group = igk_db_table_select_row(igk_db_get_table_name(IGK_TB_GROUPS), array(IGK_FD_NAME => $groupname));
    $ctrl = $ctrl ?? igk_db_get_datatableowner(igk_db_get_table_name(IGK_TB_GROUPAUTHS));
    if ($auth && $group) {
        return Groupauthorizations::insertIfNotExists(
            array(
                IGK_FD_AUTH_ID => $auth->clId,
                IGK_FD_GROUP_ID => $group->clId,
                "clGrant" => $access
            )
        );
    }
    // return igk_db_insert_if_not_exists($ctrl, igk_db_get_table_name(IGK_TB_GROUPAUTHS), array(
    //     IGK_FD_AUTH_ID => $auth->clId,
    //     IGK_FD_GROUP_ID => $group->clId,
    //     "clGrant" => $access
    // ));
    return 0;
}


///<summary></summary>
///<param name="tb"></param>
///<param name="dataobj" default="null"></param>
/**
 * 
 * @param mixed $tb 
 * @param mixed $dataobj 
 */
function igk_db_create_obj_from_infokey($tb, $dataobj = null)
{
    if ($tb) {
        $obj = igk_createobj();
        foreach ($tb as $k => $v) {
            $obj->$k = igk_db_getdefaultv($v);
        }
        if ($dataobj != null) {
            if (is_array($dataobj))
                $dataobj = (object)$dataobj;
            igk_db_copy_row($obj, $dataobj);
        }
        return $obj;
    }
    return null;
}

///<summary>return column info association keys</summary>
/**
 * return column info association keys
 * @deprecated
 */
function igk_db_column_info($db, $tablename, &$autoinc = null)
{
    $t = igk_getv(igk_getv($db->Data, $tablename), "ColumnInfo");
    $tt = [];
    foreach ($t as $v) {
        $tt[$v->clName] = $v;
        if ($v->clAutoIncrement) {
            $autoinc = $v->clName;
        }
    }
    return $tt;
}


///<summary></summary>
///<param name="ctrlorName"></param>
/**
 * 
 * @param mixed $ctrlorName 
 */
function igk_db_close($ctrlorName)
{
    $apt = igk_get_data_adapter($ctrlorName);
    if ($apt)
        $apt->close(false);
}
///<summary>close adapter</summary>
/**
 * close adapter
 */
function igk_db_close_adapter($ctrlOrAdapterName)
{
    $ad = igk_get_data_adapter($ctrlOrAdapterName);
    if ($ad) {
        $ad->close();
    }
}


// --run db-management.php table 
if (!function_exists('igk_db_command_table')) {
    /**
     * create table command 
     */
    function igk_db_command_table(BaseController $controller, string $list)
    {

        $updated = false;
        $c = explode(',', $list);
        $definition = $controller->loadDataFromSchemas();
        $tables = $definition->tables;
        $utils = new DbSchemaUtility($controller);
        $cb = $utils->load();
        $schema = igk_getv($cb->getElementsByTagName(DbSchemas::RT_SCHEMA_TAG), 0);
        while (count($c) > 0) {
            $q = array_shift($c);
            if (empty($q)) {
                continue;
            }
            $cf = '%prefix%' . $q;
            $tb = IGKSysUtil::DBGetTableName($cf, $controller);
            if (!isset($tables[$tb])) {

                $table = $schema->add(DbSchemas::DATA_DEFINITION);
                $table['TableName'] = $cf;
                $table['Description'] = "";
                Logger::info('append -> ' . $tb);
                $cl = $table->add(DbSchemas::COLUMN_TAG);
                $cl["clName"] = "id";
                $cl["clAutoIncrement"] = true;
                $cl["clNotNull"] = true;
                $cl["clIsUnique"] = true;
                $gen = $table->add(DbSchemas::GEN_COLUMNS);
                $gen["name"] = "updatetime";
                $updated = true;
            } else {
                Logger::warn('already defined -> ' . $tb);
            }
        }

        if ($updated) {
            $utils->store($cb);
            Logger::success('updated -> ' . $utils->file);
        }
    }
}

// + | --------------------------------------------------------------------
// + | db add column to table
// + |
if (!function_exists('igk_db_command_column')) {
    /**
     * add column definition to table
     */
    function igk_db_command_column(BaseController $controller, string $table, string $columndef)
    {
        $search = [$table];
        $sp = "%prefix%";
        if (!igk_str_startwith($table, $sp)) {
            $search[] = $sp . $table;
        }
        $definition = $controller->loadDataFromSchemas();
        $tables = $definition->tables;
        $tb = null;
        $n = null;
        $tv = array_values($tables);
        while (count($tv) > 0) {
            $tt = array_shift($tv);

            foreach ($search as $n) {
                if (($n == $tt->defTableName) || ($n == $tt->tableName)) {
                    $tv = [];
                    $tb = $tt;
                    break;
                }
            }
        }
        if (!$tb) {
            return false;
        }
        $v_inf = igk_db_parse_column_def_arg($columndef);

        $utils = new DbSchemaUtility($controller);
        $cb = $utils->load();
        $defs = $cb->getElementsByTagName(DbSchemas::DATA_DEFINITION);
        $store = false;
        while (count($defs) > 0) {
            $q = array_shift($defs);
            if ($q['TableName'] == $n) {
                foreach ($v_inf as $key => $cl) {
                    if (!isset($tb->columnInfo[$key])) {
                        $tc = $q->add(DbSchemas::COLUMN_TAG);
                        $tc->setAttributes((array)$cl);
                        $store = true;
                    }
                }
                break;
            }
        }
        if ($store){
            $utils->store($cb);
            Logger::success('updated -> ' . $utils->file);
        }
        return $store;
    }
}

if (!function_exists('igk_db_parse_column_def_arg')) {
    /**
     * parse column definition command argument
     * @param string $column_definition 
     * @param array<string,string> $column_definition 
     * @return array 
     * @example column[;column2[,type(length)],autoincrement,index,primary,description:];column3
     */
    function igk_db_parse_column_def_arg(string $column_definition, ?array $string_data=null)
    {
        $string_data = $string_data ?? [];
        $column_definition = preg_replace_callback("/('|\").*(?:\\1)/", function($m)use(& $string_data){
            $ln = count($string_data); 
            $string_data["$".$ln] = addslashes(igk_str_remove_quote($m[0]));
            return "$".$ln;
        }, stripslashes($column_definition));

        $cmd = array_map(function ($i) use ($string_data) {
            $tb = explode(',', $i);
            $column = array_shift($tb);
            $c = new DbColumnInfo;
            $c->clName = $column;
            if (count($tb) > 0) {
                // load definition 
                while (count($tb) > 0) {
                    $q = array_shift($tb);
                    switch ($q) {
                        case 'unique':
                            $c->clIsUnique = true;
                            break;
                        case 'autoincrement':
                            $c->clAutoIncrement = true;
                            break;
                        case 'primary':
                            $c->clIsPrimary = true;
                            break;
                        case 'index':
                            $c->clIsIndex = true;
                            break;
                        default:
                            if (preg_match("/(?P<type>\w+)\((?P<length>\d+)\)/", $q, $tab)){
                                $c->clType = $tab['type'];
                                $c->clTypeLength = intval($tab['length']);
                            } else {
                                $tab = explode(":", $q, 2);
                                $n = $tab[0];
                                $v = igk_getv($tab, 1);
                                if ($v){
                                    $v = preg_replace_callback('/\$\d+/', function($m)use($string_data){
                                        return $string_data[$m[0]];
                                    }, $v);
                                }
                                switch ($n) {
                                    case 'description':
                                        $c->clDescription = $v;
                                        break;
                                    
                                    default:
                                        # code...
                                        break;
                                }
                            }  

                            break;
                    }
                }
            }
            return [$column => $c];
        }, explode(';', $column_definition));

        $cmd = array_merge(...$cmd);
        return $cmd;
    }
}
