<?php
//
// author: C.A.D. BONDJE DOUE
// licence: IGKDEV - Balafon @ 2019
// desc: mysql data adapter
// file: igk_mysql_db.php
 
use IGK\Database\DbQueryDriver; 
use IGK\System\Configuration\Controllers\ConfigControllerRegistry;
 


if(!extension_loaded("mysql") && (!extension_loaded("mysqli"))){
    error_log("[BLF] - no extension mysql or mysqli installed. class will not be installed in that case.". extension_loaded("mysqli"));
    return;
}
if(!function_exists("mysqli_connect")){
    error_log("function not exists mysqli_connect");
    igk_exit();
}

require_once(__DIR__."/DataAdapter.php");
require_once(__DIR__."/Controllers/MySQLDataController.php");

///<summary></summary>
///<param name="srv"></param>
///<param name="dbu"></param>
///<param name="pwd"></param>
/**
* 
* @param mixed|object $srv
* @param mixed $dbu
* @param mixed $pwd
*/
function igk_db_connect($srv, $dbu=null, $pwd=null, $options=null){
 
    if(empty($srv))
        return false;
    $g=DbQueryDriver::GetFunc("connect") ?? igk_die("not connect found for !!!! ".DbQueryDriver::$Config["system"]);

    /**
     * @var object|resource $b resource type
     */
    $b = null;
    if(DbQueryDriver::Is("MySQLI")){
        try{
        if (is_object($srv)){
            if (empty($port = $srv->port)){
                $port = null;
            }
            $mg = [
                $srv->server,
                $srv->user,
                $srv->pwd,
                null,
                $port
            ];
            $b = @$g(...$mg);
        } else {
            $b = @$g($srv, $dbu, $pwd);
        }

            if (is_resource($b)){
                if ( $options && isset($options["charset"])){
                    mysqli_set_charset($b, $options["charset"]);
                }else {
                    mysqli_set_charset($b, "utf8");
                }
            }
            return $b;
        }
        catch (\Exception $ex){
            igk_ilog("DB Error : ".$ex->getMessage());
        }
        return null;
    }
    return @$g($srv, $dbu, $pwd);
}
///<summary></summary>
///<param name="v"></param>
///<param name="r" default="null"></param>
/**
* 
* @param mixed $v
* @param mixed $r the default value is null
*/
function igk_db_escape_string($v, $r=null){
    $g=DbQueryDriver::GetFunc("escapestring");
    if(DbQueryDriver::Is("MySQLI")){
        $b=DbQueryDriver::GetResId();
        if($b){
			if (is_array($v)){
                if (!igk_environment()->is("production")){
                    igk_trace();
                    igk_wln_e("Passing Array not allowed", $v);
                }                
				igk_die("escape failed: Array for value not allowed");
			}
            return $g($b, $v);
		}
        // no driver to espace string default function
        return addslashes($v); 
    }
    if(!empty($g))
        return $g($v);
    return null;
}
///<summary></summary>
///<param name="r"></param>
/**
* 
* @param mixed $r
*/
function igk_db_fetch_field($r){
    $g=DbQueryDriver::GetFunc("fetch_field");
    return $g($r);
}
///<summary></summary>
///<param name="r"></param>
/**
* 
* @param mixed $r
*/
function igk_db_fetch_row($r){
    $g=DbQueryDriver::GetFunc("fetch_row");
    return $g($r);
}
///<summary></summary>
///<param name="r"></param>
/**
* 
* @param mixed $r
*/
function igk_db_is_resource($r){
    if(DbQueryDriver::Is("MySQLI")){
        return is_object($r);
    }
    return is_resource($r);
}
///<summary></summary>
///<param name="r"></param>
/**
* 
* @param mixed $r
*/
function igk_db_num_fields($r){
    $g=DbQueryDriver::GetFunc("num_fields");
    return $g($r);
}
///<summary></summary>
///<param name="r"></param>
/**
* 
* @param mixed $r
*/
function igk_db_num_rows($r){
    $g=DbQueryDriver::GetFunc("num_rows");
    return $g($r);
}
///<summary></summary>
///<param name="query"></param>
/**
* 
* @param mixed $query
*/
function igk_db_query($query, $res=null){
    $g=DbQueryDriver::GetFunc("query");
    if(DbQueryDriver::Is("MySQLI")){
        $d=DbQueryDriver::GetResId();
        if($d)
            return $g($d, $query);
        else{
            igk_debug_wln("res id is null to send query ".$query);
        }
        return null;
    }
    return $g($query);
}
function igk_db_multi_query($query, $res=null){
	$g=DbQueryDriver::GetFunc("multi_query");
    if(DbQueryDriver::Is("MySQLI")){
        $d= $res ? $res : DbQueryDriver::GetResId();
        if($d){
            return $g($d, $query);
		}
        else{
            igk_debug_wln("res id is null to send query ".$query);
        }
        return null;
    }
    return $g($query);
}
///<summary>retreive the current server date </summary>
/**
* retreive the current server date
*/
function igk_mysql_datetime_now(){
    return date(IGK_MYSQL_DATETIME_FORMAT);
}
///<summary></summary>
///<param name="r"></param>
/**
* 
* @param mixed $r
*/
function igk_mysql_db_close($r){
    $g=DbQueryDriver::GetFunc("close");
    return @$g($r);
}
///<summary></summary>
///<param name="r" default="null"></param>
/**
* 
* @param mixed $r the default value is null
*/
function igk_mysql_db_error($r=null){
    $g=DbQueryDriver::GetFunc("error");
    if(DbQueryDriver::Is("MySQLI")){
        $d=DbQueryDriver::GetResId();
        if($d)
            return $g($d);
        return "";
    }
    return $g($r);
}
///<summary></summary>
/**
* 
*/
function igk_mysql_db_errorc(){
    $g=DbQueryDriver::GetFunc("errorc");
    $r =$d=DbQueryDriver::GetResId();
    if(DbQueryDriver::Is("MySQLI")){
        if($d)
            return $g($d);
        return "";
    }
    return $g($r);
}
///<summary></summary>
///<param name="t"></param>
/**
* 
* @param mixed $t
*/
function igk_mysql_db_gettypename($t){
    $m_mysqli_data=array(
            1=>'boolean',
            2=>'smallint',
            3=>'int',
            4=>'float',
            5=>'double',
            10=>'date',
            11=>'time',
            12=>'datetime',
            252=>'text',
            253=>'varchar',
            254=>'binary'
        );
    if(is_numeric($t)){
        return igk_getv($m_mysqli_data, $t);
    }
    return $t;
}
///<summary></summary>
/**
* 
*/
function igk_mysql_db_has_error(){
    return igk_mysql_db_errorc() != 0;
}
///<summary></summary>
///<param name="flags"></param>
/**
* 
* @param mixed $flags
*/
function igk_mysql_db_is_primary_key($flags){
    return ($flags& 2) == 2;
}
///<summary></summary>
///<param name="r" default="null"></param>
/**
* 
* @param mixed $r the default value is null
*/
function igk_mysql_db_last_id($r=null){
    $g=DbQueryDriver::GetFunc("lastid");
    if(DbQueryDriver::Is("MySQLI")){
        $d=DbQueryDriver::GetResId();
        if($d){
            return $g($d);
        }
        else{
            return -1;
        }
    }
    return $g($r);
}
///<summary></summary>
///<param name="mysql"></param>
/**
* 
* @param mixed $mysql
*/
function igk_mysql_db_selected_db($mysql){
    $r=$mysql->sendQuery("SELECT DATABASE();")->getRowAtIndex(0);
    $c="DATABASE()";
    return $r->$c;
}
///<summary></summary>
///<param name="tbname"></param>
/**
* 
* @param mixed $tbname
*/
function igk_mysql_db_tbname($tbname){
    return igk_db_escape_string(igk_db_get_table_name($tbname));
}
///<summary></summary>
///<param name="resource"></param>
/**
* 
* @param mixed $resource
*/
function igk_mysql_result_table($resource){
    $tab=igk_createnode("table");
    $tab["class"]="igk-table mysql-r";
    $tr=$tab->Add("tr");
    $c=igk_db_num_fields($resource);
    for($i=0; $i < igk_db_num_fields($resource); $i++){
        $tr->Add("th")->Content= mysqli_field_seek($resource, $i);
    }
    foreach(mysqli_fetch_assoc($resource) as $k){
        $tr=$tab->Add("tr");
        if(is_array($k)){
            foreach($k as $v){
                $tr->Add("td")->Content=$v;
            }
        }
        else{
            $tr->Add("td")->Content=$k;
        }
    }
    return $tab;
}
///<summary></summary>
///<param name="date"></param>
/**
* 
* @param mixed $date
*/
function igk_mysql_time_span($date){
    return igk_time_span(IGK_MYSQL_DATETIME_FORMAT, $date);
}






///<summary> represent multi query access </summary>
function igk_mysqli_multi_query($con, $query){
	 $cr =  mysqli_multi_query($con, $query);
	 if ($cr){
		 while (mysqli_more_results($con) && mysqli_next_result($con)){
		 }
		 $cr = ($error = mysqli_errno($con)) == 0;
	 }
	 return $cr;
}
define("IGK_MSQL_DB_Adapter", 1);
define("IGK_MSQL_DB_AdapterFunc", extension_loaded("mysql"));
define("IGK_MSQLi_DB_AdapterFunc", extension_loaded("mysqli"));
if(IGK_MSQLi_DB_AdapterFunc){
    define("IGK_MYSQL_USAGE", "MySQLi");
}
else
    define("IGK_MYSQL_USAGE", "MySQL");
define("IGK_MYSQL_DATETIME_FORMAT", "Y-m-d H:i:s");
define("IGK_MYSQL_TIME_FORMAT", IGK_MYSQL_DATETIME_FORMAT);
define("IGK_MYSQL_DATE_FORMAT", "Y-m-d");
DbQueryDriver::Init(function(& $conf){
    $n="mysqli";
    $conf["system"]="mysqli";
    $conf[$n]["func"]=array();
    $conf[$n]["auto_increment_word"]="AUTO_INCREMENT";
    $conf[$n]["data_adapter"]="MYSQL";
    $t=array();
    $t["connect"]="mysqli_connect";
    $t["selectdb"]="mysqli_select_db";
    $t["check_connect"]="mysqli_ping";
    $t["query"]="mysqli_query";
    $t["multi_query"]="igk_mysqli_multi_query";
    $t["escapestring"]="mysqli_real_escape_string";
    $t["num_rows"]="mysqli_num_rows";
    $t["num_fields"]="mysqli_num_fields";
    $t["fetch_field"]="mysqli_fetch_field";
    $t["fetch_row"]="mysqli_fetch_row";
    $t["close"]="mysqli_close";
    $t["error"]="mysqli_error";
    $t["errorc"]="mysqli_errno";
    $t["lastid"]="mysqli_insert_id";
    $conf[$n]["func"]=$t;
});

require_once __DIR__ ."/Controllers/DbConfigController.php";

ConfigControllerRegistry::Register(\IGK\System\Database\MySQL\Controllers\DbConfigController::class, IGK_MYSQL_DB_CTRL);