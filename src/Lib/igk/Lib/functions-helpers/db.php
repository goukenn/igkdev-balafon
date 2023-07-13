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

use IGK\Database\DbSchemas;

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
        if ($ctrl)
            igk_db_insert($ctrl, $authTable, $p);
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
 * 
 * @param mixed $ctrl 
 * @param mixed $entries 
 * @deprecated use direct Model access
 */
function igk_db_insertc($ctrl, $entries)
{
    return igk_db_insert($ctrl, $ctrl->getDataTableName(), $entries, null);
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
    if ($auth && $group)
        return igk_db_insert_if_not_exists($ctrl, igk_db_get_table_name(IGK_TB_GROUPAUTHS), array(
            IGK_FD_AUTH_ID => $auth->clId,
            IGK_FD_GROUP_ID => $group->clId,
            "clGrant" => $access
        ));
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