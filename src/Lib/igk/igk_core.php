<?php
// @file: igk_core.php
// @author: C.A.D. BONDJE DOUE
// @description: core function and initialiation
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

defined("IGK_FRAMEWORK") || die("REQUIRE FRAMEWORK - No direct access allowed");


use IGK\Resources\R;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Http\RequestHandler;
use IGK\System\IO\FileWriter as File;
use IGK\System\IO\FileWriter;
use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\Helper\StringUtility as IGKString;
use IGK\Helper\SysUtils;
use IGK\System\IO\Path;

use function igk_resources_gets  as __;


///<summary>shortcut to get server info</summary>
/**
 * shortcut to get server info
 * @return IGKServer server
 */
function igk_server()
{
    return IGKServer::getInstance();
}
///<summary></summary>
/**
 * shortcut get core environment 
 * @return IGKEnvironment environment
 */
function igk_environment()
{
    return IGKEnvironment::getInstance();
}
/**
 * return application configuration 
 * @return mixed 
 */
function igk_sys_configs(){
    return IGKAppConfig::getInstance()->Data;
}



///<summary> encapsulate exit function. used for debugging purpose</summary>
/**
 *  encapsulate exit function. used for debugging purpose
 *  @throws Exception
 *  @endcode exit
 */
function igk_exit($close = 1, $clean_buffer = 0)
 {
    if (igk_environment()->isAJXDemand){
        if (igk_environment()->is("DEV")){
            igk_trace();
            igk_wln("<div style='position:fixed; z-index: 1000; top:0; left:0: background-color:red; color:#fdfdfd'>call igk_exit not allowed in : inAJXDemand Flag context.</div>");
        }
    }
    if ($close && !empty(session_id())) {
        igk_hook(IGKEvents::ON_BEFORE_EXIT, array(igk_app(), null));
        session_write_close();
        unset($_SESSION);
    }
    exit;
}
///<summary>get output type</summary>
/**
 * get zip output type
 */
function igk_zip_output_type($forcegzip=0){
    $accept = igk_getv($_SERVER, 'HTTP_ACCEPT_ENCODING', 0);
    $type = null;
    if (!$forcegzip && strstr($accept, "deflate") && function_exists("gzdeflate")) {         
        $type = "deflate";
    } else if (($forcegzip || strstr($accept, "gzip")) && function_exists("gzencode")) {        
        $type = "gzip";
    } else {
        $type = 'no-compression';        
    }
    return $type;
}
///<summary>write zipped output to buffer</summary>
function igk_zip_output($c, $forcegzip = 0, $header = 1, &$type = null)
{
    $accept = igk_getv($_SERVER, 'HTTP_ACCEPT_ENCODING', 0);
    if (!$forcegzip && strstr($accept, "deflate") && function_exists("gzdeflate")) {
        if ($header) {
            header('Content-Encoding: deflate');
        }
        igk_wl(gzdeflate($c, 3));
        $type = "deflate";
    } else if (($forcegzip || strstr($accept, "gzip")) && function_exists("gzencode")) {
        if ($header)
            header('Content-Encoding: gzip');
        igk_wl(gzencode($c, 3));
        $type = "gzip";
    } else {
        $type = 'no-compression';
        igk_wl($c);
    }
}
///<summary>die with message</summary>
///<param name="msg">mixed value. error_array|string.</param>
///<param name="throwex">bool throw exception</param>
/**
 * die with message
 * @param mixed $msg value. error_array|string.
 * @param mixed $throwex bool throw exception
 * @throws Exception
 */
function igk_die($msg = IGK_DIE_DEFAULT_MSG, $throwex = 1, $code = 400)
{
    if ($throwex) {
        if (is_array($msg)) {
            $t = $msg;
            $msg = "";
            if (isset($t["code"]))
                $msg .= "<div>code:{$t['code']}</div>";
            if (isset($t["message"]) && ($m = $t["message"])) {
                if (is_array($m)) {
                    $msg .= "<div>Message: ";
                    foreach ($m as $r) {
                        $msg .= "<li>" . $r . "</li>";
                    }
                    $msg .= "</div>";
                } else {
                    $msg .= "<div>Message: {$m} </div>";
                }
            }
        }
        error_log($msg);
        // + | Last Exception         
        throw new IGKException($msg, $code);
    } else {
        ob_clean();
        igk_set_header($code);
        echo $msg;
        igk_exit();
    }
}
///<summary>shortcut to resource get __</summary>
/**
 * shortcut to resource get __
 * @param string $text formatted key
 * @param string|null $default default value
 */
function igk_resources_gets($text, $default = null)
{
    return call_user_func_array(array(R::class, 'gets'), func_get_args());
}
///<summary> get value in array</summary>
///<param name="default"> mixed, default value or callback expression </param>
/**
 *  get value in array
 * @param mixed default value or callback expression
 */
function igk_getv($array, $key, $default = null)
{
    return igk_getpv($array, array($key), $default);
}

///<summary></summary>
///<param name="array"></param>
///<param name="key"></param>
///<param name="default" default="null"></param>
/**
 * 
 * @param mixed $array
 * @param mixed $key
 * @param mixed $default the default value is null
 */
function igk_getpv($array, $key, $default = null)
{
    $n = $key;
    if (!is_array($n)) {
        $n = explode("/", $n);
    }
    if (($array === null) || (empty($key) && ($key !== 0))) {
        return $default;
    }
    if ($key === null) {
        igk_die(__FUNCTION__ . " key not defined");
    }
    $def = $default;
    $o = null;
    $ckey = "";

    // $v_isobj = is_object($array);
    // if ($v_isobj){
    //     echo ("for ". get_class($array) . "<br />");
    //     echo "demand for is array ? ".is_array($array);
    // }



    //, $key, $n);
    while ($array && (($q = array_shift($n)) || ($q === 0))) {
        $o = null;
        $ckey = $q;
        if (is_array($array) && isset($array[$q])) {
            $o = $array[$q];
        } else if (is_object($array)) {
            if (isset($array->$q)) {
                $o = $array->$q;
            } else {
                $t = class_implements(get_class($array));
                if (isset($t[ArrayAccess::class])) {
                    $o = $array[$q];
                }
            }
        }
        $array = $o;
    }
    if ($o === null) {
        if (!is_string($def) && igk_is_callable($def)) {
            $o = call_user_func_array($def, array());
            $array[$ckey] = $o;
        } else {
            $o = $def;
        }
    }
    return $o;
}

///<summary>autoload class function</summary>
/**
 * autoload class in dirs
 */
function igk_auto_load_class($name, $entryNS, $classdir, &$refile = null)
{
    return IGKApplicationLoader::getInstance()->registerLoading($name, $entryNS, $classdir, $refile);   
}
function igk_io_get_script($f, $args = null)
{
    if (file_exists($f)) {
        return "?>" . file_get_contents($f);
    }
    return null;
}
// function & igk_to_array($tab){
// 	$t = (array)$tab;
//     return $t;
// }

///<summary>evalute constant and get the value</summary>
///<return>null if constant not defined</return>
/**
 * evalute constant and get the value
 */
function igk_const($n)
{
    if (defined($n)) {
        return constant($n);
    }
    return null;
}
/**
 * check value for assertion
 * @return void 
 */
function igk_check($b): bool
{
    switch (true) {
        case is_bool($b):
            return $b;
        case is_object($b):
            if (method_exists($b, "success")) {
                return $b->success();
            }
            return true;
        case is_array($b):
            return !empty($b);
    }
    return false;
}
///<summary>check if a constant match the defvalue</summary>
/**
 * check if a constant match the defvalue
 */
function igk_const_defined($ctname, $defvalue = 1)
{
    if (defined($ctname))
        return constant($ctname) == $defvalue;
    return false;
}
///<summary></summary>
///<param name="class"></param>
///<param name="obj" ref="true"></param>
///<param name="callback"></param>
/**
 * 
 * @param mixed $class
 * @param  * $obj
 * @param mixed $callback
 */
function igk_create_instance($class, &$obj, $callback)
{
    if ($obj === null) {
        $obj = $callback($class);
    }
    return $obj;
}


///get basename without extension
/**
 */
function igk_io_basenamewithoutext($file)
{
    return igk_io_remove_ext(basename($file));
}
///<summary></summary>
///<param name="fname"></param>
/**
 * 
 * @param mixed $fname
 */
function igk_io_path_ext($fname)
{
    if (empty($fname))
        return null;
    return ($t = explode(".", $fname)) > 1 ? array_pop($t) : "";
}
///<summary>Remove extension from filename @name file name</summary>
/**
 * Remove extension from filename @name file name
 */
function igk_io_remove_ext($name)
{
    if (empty($name))
        return null;
    $t = explode(".", $name);
    if (count($t) > 1) {
        $s = substr($name, 0, strlen($name) - strlen($t[count($t) - 1]) - 1);
        return $s;
    }
    return $name;
}
function igk_io_inject_uri_arg($uri, $name, &$fragment = null)
{
    $g = parse_url($uri);
    if (!empty($fragment = igk_getv($g, "fragment"))) {
        $fragment = "#" . $fragment;
    }
    $uri = explode("?", $uri)[0] . "?";
    if (!empty($query = igk_getv($g, "query"))) {
        parse_str($query, $info);
        unset($info[$name]);
        $uri = explode("?", $uri)[0] . "?" . http_build_query($info) . "&";
    }
    return $uri;
}
/**
 * build info query args
 */
function igk_io_build_uri($uri, ?array $query = null, &$fragment = null)
{
    $g = parse_url($uri);
    if (!empty($fragment = igk_getv($g, "fragment"))) {
        $fragment = "#" . $fragment;
    }
    $info = $query ?? [];
    $uri = explode("?", $uri)[0];
    if (!empty($tquery = igk_getv($g, "query"))) {
        parse_str($tquery, $info);
        if ($info && $query) {
            $info = array_merge($info, $query);
        }
    }
    $uri = $uri . "?" . http_build_query($info);
    return $uri;
}
function igk_io_syspath($relativepath = null)
{
    if ($relativepath)
        return igk_io_dir(igk_io_applicationdir() . "/" . $relativepath);
    return igk_io_applicationdir();
}
///<summary></summary>
/**
 * 
 */
function igk_io_applicationdir()
{
    return Path::getInstance()->getApplicationDir();
}

///<summary>detect that the environment in on mand line mode</summary>
/**
 * detect that the environment in on command line mode
 */
function igk_is_cmd()
{
    return ((isset($_SERVER["argv"]) && !isset($_SERVER["SERVER_PROTOCOL"]))) || igk_environment()->get("sys://func/" . __FUNCTION__);
}
function igk_set_cmd($v = 1)
{
    igk_environment()->set("sys://func/igk_is_cmd", $v);
}
function igk_is_null_or_empty($c)
{
    return ($c === null) || empty($c);
}

///<summary>get if framework is in atomic mode</summary>
/**
 * get if framework is in atomic mode
 */
function igk_is_atomic()
{
    return defined("IGK_FRAMEWORK_ATOMIC") && (IGK_FRAMEWORK_ATOMIC == 1);
}

///<summary></summary>
///<param name="name"></param>
/**
 * 
 * @param mixed $name
 */
function igk_load_library($name)
{
    static $inUse = null;
    if ($inUse === null) {
        $inUse = array();
    }
    $lib = IGK_LIB_DIR . "/Library/";
    $c = $lib . "/igk_" . $name . ".php";
    $ext = igk_io_path_ext(basename($name));
    if (empty($ext) || ($ext != ".php"))
        $ext = ".php";
    if ((file_exists($c) || file_exists($c = $lib . "/" . $name . $ext)) && !isset($inUse[$c])) {
        include_once($c);
        $inUse[$c] = 1;
        return 1;
    }
    return 0;
}

function igk_wl_tag($tag)
{
    echo "<$tag>";
    foreach (array_slice($tab = func_get_args(), 1) as $c) {
        igk_wl($c);
    }
    echo "</$tag>";
}


///<summary>download zip core </summary>
/**
 * download zip core
 */
function igk_sys_download_core($download = 1)
{
    $tfile = tempnam(sys_get_temp_dir(), "igk");
    $zip = new ZipArchive();
    if ($zip->open($tfile, ZIPARCHIVE::CREATE)) {
        igk_zip_dir(IGK_LIB_DIR, $zip, "Lib/igk", "/\.(vscode|git|gkds)$/");
        $manifest = igk_create_xmlnode("manifest");
        $manifest["xmlns"] = "https://www.igkdev.com/balafon/schemas/manifest";
        $manifest["appName"] = IGK_PLATEFORM_NAME;
        $manifest->add("version")->Content = IGK_VERSION;
        $manifest->add("author")->Content = IGK_AUTHOR;
        $manifest->add("date")->Content = date("Ymd His");
        $zip->addFromString("manifest.xml", $manifest->render());
        $zip->addFromString("__lib.def", "");
        $zip->close();
    }
    if ($download)
        igk_download_file("Balafon." . IGK_VERSION . ".zip", $tfile, "binary", 0);
    return $tfile;
}


///<summary>return a list of controller installed in project dirs </summary>
/**
 * 
 * @return array|IGK\Controllers\BaseController[] list of controller  
 * @throws IGKException 
 */
function igk_sys_project_controllers()
{
    return SysUtils::GetProjectControllers();       
}
///<summary></summary>
///<param name="msg"></param>
/**
 * 
 * @param mixed $msg
 */
function igk_wl($msg)
{
    /// TODO: BIND TRACE do not use include for speed
    if ((igk_const_defined('IGK_ENV_NO_TRACE_KEY') && igk_environment()->get(IGK_ENV_NO_TRACE_KEY) != 1) && igk_const_defined("IGK_TRACE", 1)) {
        $lv = igk_environment()->get('TRACE_LEVEL', igk_environment()->get(IGK_ENV_TRACE_LEVEL, 2));
        $c = IGKException::GetCallingFunction($lv);
        if (igk_is_cmd()) {
            $cp = (object)[];
            foreach ($c as $k => $v) {
                $cp->$k = $v;

                if ($k == "function") {
                    echo implode(":", (array)$cp) . PHP_EOL;
                }
            }
        } else {
            $dn = '<div>';
            $dn .= '<table class="igk-table-hover igk-table-striped" >';
            $r1 = '<tr>';
            $r2 = '<tr>';
            foreach ($c as $k => $v) {
                $r1 .= '<th>' . $k . '</th>';
                $r2 .= '<td>' . $v . '</td>';
            }
            $dn = $r1 . $r2 . '<table></div>';
            echo $dn;
        }
    }
    $tab = func_get_args();
    while ($msg = array_shift($tab)) {
        if (is_array($msg) || is_object($msg)) {
            igk_log_var_dump($msg);
        } else
            echo $msg;
    }
}
///<summary></summary>
///<param name="p"></param>
/**
 * 
 * @param mixed $p
 */
function igk_wl_pre($p)
{
    echo "<pre>";
    print_r($p);
    echo "</pre>";
}
function igk_dump_pre($p)
{
    echo "<pre>";
    var_dump($p);
    echo "</pre>";
}
function igk_dev_wln()
{
    if (igk_environment()->is("DEV")) {
        call_user_func_array("igk_wln", func_get_args());
    }
}
function igk_dev_ilog()
{
    if (igk_environment()->is("DEV")) {
        call_user_func_array("igk_ilog", func_get_args());
    }
}
function igk_dev_wln_e()
{
    if (igk_environment()->is("DEV")) {
        call_user_func_array("igk_wln_e", func_get_args());
        igk_exit();
    }
}
// function igk_wln_set($prop, $value){
//     $s = igk_env_get($k = "sys://igk_wln");
//     if ($s === null)
//         $s = [];
//     if ($value == null){
//         unset($s[$prop]);
//     }else
//         $s[$prop] = $value;
//     igk_env_set($k, $s);
// }


function igk_bind_trace()
{

    if ((igk_const_defined('IGK_ENV_NO_TRACE_KEY') && igk_environment()->get(IGK_ENV_NO_TRACE_KEY) != 1) && igk_const_defined("IGK_TRACE", 1)) {
        $lv = igk_environment()->get('TRACE_LEVEL', igk_environment()->get(IGK_ENV_TRACE_LEVEL, 2));
        $c = IGKException::GetCallingFunction($lv);
        if (igk_is_cmd()) {
            $cp = (object)[];
            foreach ($c as $k => $v) {
                $cp->$k = $v;

                if ($k == "function") {
                    echo implode(":", (array)$cp) . PHP_EOL;
                }
            }
        } else {
            $dn = '<div>';
            $dn .= '<table class="igk-table-hover igk-table-striped" >';
            $r1 = '<tr>';
            $r2 = '<tr>';
            foreach ($c as $k => $v) {
                $r1 .= '<th>' . $k . '</th>';
                $r2 .= '<td>' . $v . '</td>';
            }
            $dn = $r1 . $r2 . '<table></div>';
            echo $dn;
        }
    }
}
///<summary></summary>
///<param name="msg" default=""></param>
/**
 * 
 * @param string|mixed $msg the default value is ""
 */
function igk_wln($msg = "")
{


    //if (file_exists(IGK_LIB_DIR.'/Inc/igk_trace.pinc')){
    // include(IGK_LIB_DIR.'/Inc/igk_trace.pinc');
    //}
    /// BIND TRACE IF - do not include file for speed 
    igk_bind_trace(3);
    if ((igk_const_defined('IGK_ENV_NO_TRACE_KEY') && igk_environment()->get(IGK_ENV_NO_TRACE_KEY) != 1) && igk_const_defined("IGK_TRACE", 1)) {
        $lv = igk_environment()->get('TRACE_LEVEL', igk_environment()->get(IGK_ENV_TRACE_LEVEL, 2));
        $c = IGKException::GetCallingFunction($lv);
        if (igk_is_cmd()) {
            $cp = (object)[];
            foreach ($c as $k => $v) {
                $cp->$k = $v;

                if ($k == "function") {
                    echo implode(":", (array)$cp) . PHP_EOL;
                }
            }
        } else {
            $dn = '<div>';
            $dn .= '<table class="igk-table-hover igk-table-striped" >';
            $r1 = '<tr>';
            $r2 = '<tr>';
            foreach ($c as $k => $v) {
                $r1 .= '<th>' . $k . '</th>';
                $r2 .= '<td>' . $v . '</td>';
            }
            $dn = $r1 . $r2 . '<table></div>';
            echo $dn;
        }
    }

    // $LF = igk_getv($options =  igk_environment->get("sys://igk_wln"), "lf", "<br />");

    if (!($lf = igk_environment()->get(IGK_LF_KEY))) {
        $v_iscmd = igk_is_cmd();
        $lf = $v_iscmd ? IGK_CLF : "<br />";
    }

    foreach (func_get_args() as $k) {
        $msg = $k;
        if (is_string($msg) || is_numeric($msg))
            echo ($msg . $lf);
        else {
            if ($msg !== null) {
                if (is_object($msg)) {
                    if ($msg instanceof HtmlNode) {
                        echo ($msg->render() . $lf);
                        continue;
                    }
                    var_dump($msg);
                    echo $lf;
                } else {
                    igk_log_var_dump($msg, $lf); 
                }
            } else {
                echo (__FUNCTION__ . "::msg is null" . $lf);
            }
        }
    }
}


///<summary></summary>
///<param name="tab"></param>
/**
 * 
 * @param mixed $tab
 */
function igk_log_var_dump($tab, $lf = null)
{
    if ($lf === null) {
        if (!($lf = igk_environment()->get(IGK_LF_KEY))) {
            $v_iscmd = igk_is_cmd();
            $lf = $v_iscmd ? IGK_CLF : "<br />";
        }
    }
    if (is_numeric($tab) || is_bool($tab)) {
        igk_wl($tab);
        igk_wl($lf);
        return;
    }
    $textmode = (igk_is_cmd() || igk_environment()->get("igk_log_var_dump") == 'text');
    $cl = array("array" => "#84a");
    $s = "";
    $LF = $lf;
    $TAB = ($textmode) ? '' : "\t";
    $is_obj = is_object($tab);
    $is_cmd = $textmode;
    if ($is_obj) {
        $s .= 'Type: ' . get_class($tab);
    } else if (is_array($tab)) {
        $s .= 'Type: ';
        if (!$textmode) {
            $s .= '<span style="color: ' . $cl['array'] . '">';
        }
        $s .= " IsArray: ";
        if (!$textmode)
            $s .= "</span>";
    }
    $msg = $s . $LF . "(" . $LF;
    if ($tab) {
        foreach ($tab as $k => $v) {
            $msg .= "{$TAB}{$k}";
            if (is_object($v)) {
                $msg .= ":Object[" . get_class($v) . "]";
            } else if (is_array($v)) {
                $msg .= ":Array";
            } else
                $msg .= " => " . $v;
            $msg .= $LF;
        }
    }
    igk_wl($msg . ")" . $lf);
}
///<summary>write line to buffer and exit</summary>
/**
 * write line to buffer and exit
 */
function igk_wln_e($msg = "")
{ 
    igk_environment()->set('TRACE_LEVEL', 3);
    call_user_func_array('igk_wln', func_get_args());
    igk_exit();
}

///<summary>utility to write html content </summary>
///<param name="args"> mixed| 1 array is attribute or next is considered as content to render </summary>
function igk_tag_wln($tag, ...$args)
{
    $attr = "";
    $targs = array_slice(func_get_args(), 1);
    if (is_array($args) && (func_num_args() > 2)) {
        $attr = " " . igk_html_render_attribs($args);
        $targs = array_slice($targs, 1);
    }
    ob_start();
    call_user_func_array('igk_wln', $targs);
    $s = ob_get_contents();
    ob_end_clean();
    $o = "<{$tag}" . $attr;
    if (empty($s)) {
        $o .= "/>";
    } else {
        $o .= "> " . $s . "</{$tag}>";
    }
    igk_wl($o);
}



///<summary></summary>
///<param name="ctrl"></param>
/**
 * 
 * @param mixed $ctrl
 */
function igk_app_is_appuser($ctrl)
{
    return ($u = $ctrl->User) && $u->clLogin == $ctrl->Configs->{'app.DefaultUser'};
}
///<summary>get if application is on uri demand</summary>
/**
 * get if application is on uri demand
 */
function igk_app_is_uri_demand($app, $function)
{
    return (igk_io_currentUri() == $app->getAppUri($function));
}
///<summary>encrypt in sha256 </summary>
function igk_encrypt($data, $prefix = null)
{
    return IGKSysUtil::Encrypt($data, $prefix);
}
function igk_sys_copyright()
{
    return "IGKDEV &copy; 2011-" . date('Y') . " " . __("all rights reserved");
}


///<summary></summary>
///<param name="depth"></param>
/**
 * 
 * @param mixed $depth the default value is 0
 */
function igk_trace($depth = 0, $sep = "", $count = -1, $header = 0)
{
    $callers = debug_backtrace();
    $o = "";
    $tc = 1;

    if (igk_is_cmd()) {
        for ($i = $depth; $i < count($callers); $i++, $tc++) {
            //+ show file before line to cmd+click to be handle
            $f = igk_getv($callers[$i], "function");
            $c = igk_getv($callers[$i], "class", "__global");
            $o .= igk_getv($callers[$i], "file") . ":" . igk_getv($callers[$i], "line") . PHP_EOL;
        }
        echo $o;
        return;
    }
    $colors = ["#c0c698", "#cecece"];
    $tds = "padding:4px;";
    $o .= "<div>" . $sep;
    $o .= "<table style=\"border-collapse: collapse; min-width: 400; font-familly: sans-serif; margin:25px 0; box-shadow: 0 0 20px rgba(0, 0, 0, 0.15); \">" . $sep;

    if ($header) {

        $o .= "<tr style=\"background-color: \">";
        $o .= "<th>&nbsp;</th>";
        $o .= "<th>" . __("Line") . "</th>";
        $o .= "<th>" . __("File") . "</th>";
        $o .= "<th>" . __("Function") . "</th>";
        $o .= "<th>" . __("In") . "</th>";
        $o .= "</tr>" . $sep;
    }
    $_base_path = !igk_environment()->is("DEV") && defined("IGK_BASE_DIR");

    for ($i = $depth; $i < count($callers); $i++, $tc++) {


        $f = igk_getv($callers[$i], "function");
        $c = igk_getv($callers[$i], "class", "__global");
        $o .= "<tr style=\"background-color: ".$colors[ $i % 2] ."; border-bottom: 2px solid #f3f3f3; \">";
        $o .= "<td style=\"{$tds}\">" . $tc . "</td>";
        $o .= "<td style=\"{$tds}\">" . igk_getv($callers[$i], "line") . "</td>";
        $o .= "<td style=\"{$tds}\">";
        $g = igk_getv($callers[$i], "file");
        if ($_base_path) {
            $g = igk_io_collapse_path($g); // igk_io_basepath($g);
        }
        $o .= $g;

        $o .= "</td>";
        $o .= "<td style=\"{$tds}\">" . $f . "</td>";
        $o .= "<td style=\"{$tds}\">" . $c . "</td>";
        $o .= "</tr>" . $sep;
        if ($count > 0) {
            $count--;
            if ($count == 0)
                break;
        }
    }
    $o .= "</table>" . $sep;
    $o .= "</div>" . $sep;
    echo $o;
}
/**
 * trace and exit
 * @return never 
 * @throws IGKException 
 */
function igk_trace_e()
{
    igk_trace(1);
    igk_exit();
}

///<summary>get system directory presentation</summary>
/**
 * get system directory presentation
 */
function igk_io_dir($dir, $separator = DIRECTORY_SEPARATOR)
{
    return IO::GetDir($dir, $separator);
}
function igk_prepare_components_storage()
{
    return (object)array(
        "objs" => array(),
        "ids" => array(),
        "uris" => array(),
        "srcs" => array()
    );
}
///<summary>get system running context</summary>
/**
 * get system running context
 */
function igk_current_context()
{
    return igk_environment()->get(IGK_ENV_APP_CONTEXT, IGKAppContext::initializing);
}
///<summary>get if the system is on production mode</summary>
/**
 * get if the system is on production mode
 */
function igk_sys_env_production()
{
    return igk_environment()->is("OPS");
}

///<summary> utility function to get server name</summary>
/**
 *  utility function to get server name
 */
function igk_server_name()
{
    return igk_server()->SERVER_NAME;
}

///<summary> extend is callable function for igk usage</summary>
///<remark> used echo to write something. igk_wln failed nested looping for tracing data. because of igk_getv</remark>
/**
 *  extend is callable function for igk usage
 */
function igk_is_callable($tab)
{
    if ($tab == null)
        return 0;
    if (is_callable($tab))
        return true;
    if (is_array($tab) && count($tab) > 2) {
        $c = array_slice($tab, 0, 2);
        return is_callable($c);
    }
    return igk_is_callback_obj($tab);
}

///<summary>determine whether to only get one controller per application</summary>
/**
 * determine whether to only get one controller per application
 */
function igk_is_singlecore_app()
{
    return igk_sys_getconfig("force_single_controller_app", igk_const("IGK_SINGLE_CONTROLLER_APP"));
}


///<summary>shortcut IGKEvents::hook</summary>
///<param name="name">hook name</param>
///<param name="args">argument to pass</param>
/**
 * shortcut to IGKEvents::hook 
 * @param mixed $name
 * @param mixed $args the default value is
 * @param array|object $options to pass default|output|type
 */
function igk_hook($name, $args = array(), $options = null)
{
    return IGKEvents::hook($name, $args, $options);
}


///<summary></summary>
///<param name="name"></param>
///<param name="callback"></param>
///<param name="priority" default="10"></param>
/**
 * 
 * @param mixed $name
 * @param mixed $callback
 * @param mixed $priority the default value is 10
 */
function igk_reg_hook($name, $callback, $priority = 10)
{
    IGKEvents::reg_hook($name, $callback, $priority);
}




///<summary> return the application in the current session </summary>
/**
 *  return the application in the current session
 *  @return IGKApp
 */
function igk_app()
{
    return IGKApp::getInstance();
}
///<summary>shortcut to get controller by ref_name</summary>
/**
 * shortcut to get controller by ref_name
 * @param string $name reference name. key or class name
 * @param int|bool $throwex throw exception if not found
 * @return null|IGK\Controllers\BaseController controller found
 */
function igk_getctrl($name, $throwex = 1)
{
    return igk_app()->getControllerManager()->getController($name, $throwex);
}
/**
 * shortcut to write log
 * @param string $message 
 * @param string|null $tag 
 * @param mixed $traceindex tracing index
 * @return void 
 * @throws IGKException 
 */
function igk_ilog($message, ?string $tag=null, $traceindex=0){
    IGKLog::Append($message, $tag, $traceindex);
}

// + | IO shortcut
///<summary>shortcut get baseuri</summary>
///<param name="dir">null or existing fullpath directory or file element. </param>
///<return>full base uri path</return>
/**
 * shortcut to IO::GetBaseUri
 * @param mixed $dir null or existing fullpath directory or file element.
 */
function igk_io_baseuri($dir = null, $secured = null, &$path = null)
{
    return Path::getInstance()->baseuri($dir, $secured, $path);
}


///<summary>return the current page folder</summary>
/**
 * return the current page folder
 */
function igk_io_current_page_folder()
{
    return igk_app()->getCurrentPageFolder();
}

///<summary> get relative path to rootdir if exists</summary>
///<param name="dir">cwd path or full path</param>
///<remark>$dir must exist</remark>
/**
 * get relative path to rootdir if exists
 * @param string $dir cwd path or full path
 * @param string $sep separator
 * @return string return path
 */
function igk_io_basepath($dir, $sep = DIRECTORY_SEPARATOR)
{
    return Path::getInstance()->basepath($dir, $sep);
}
///<summary>get path to base directory</summary>
///<remark>return the directory full path according to base directory</remark>
/**
 * get path from base directory
 */
function igk_io_basedir($dir = null)
{
    return Path::getInstance()->basedir($dir);
}
///<summary>retrieve the data folder shortcut </summary>
/**
 * retrieve the data folder shortuct
 */
function igk_io_sys_datadir()
{
    return Path::getInstance()->getSysDataDir();
}

///<summary>get system configuration value</summary>
/**
 * get system configuration value
 */
function igk_sys_getconfig($name, $defaultvalue = null)
{
    return igk_getv(IGKAppConfig::getInstance()->Data, $name, $defaultvalue);
}


///<summary></summary>
///<param name="file"></param>
///<param name="content"></param>
///<param name="overwrite" default="true"></param>
///<param name="chmod" default="IGK_DEFAULT_FILE_MASK"></param>
///<param name="type" default="w+"></param>
/**
 * 
 * @param mixed $file
 * @param mixed $content
 * @param mixed $overwrite the default value is true
 * @param mixed $chmod the default value is IGK_DEFAULT_FILE_MASK
 * @param mixed $type the default value is "w+"
 */
function igk_io_w2file($file, $content, $overwrite = true, $chmod = IGK_DEFAULT_FILE_MASK, $type = "w+")
{
    return File::Save($file, $content, $overwrite, $chmod, $type);
}

function igk_get_defaultwebpagectrl()
{
    $n = IGKAppConfig::getInstance()->Data->get("default_controller");

    if (!$n || !class_exists($n, false)) {
        return null;
    } else {
        if (!is_subclass_of($n, BaseController::class)) {
            igk_die(__("Default class is not a Balafon controller : {0}", $n));
        }
        if (method_exists($n, "getInstance")) {
            return $n::getInstance();
        } else {
            $b = igk_getctrl($n);
            if ($b === null) {
                $b = igk_app()->session->createInstance($n);
            }
            return $b;
        }
    }
}

///<summary>get object with igk Xpath selection model</summary>
/**
 * get object with igk Xpath selection model
 */
function igk_conf_get($conf, $path, $default = null, $strict = 0)
{
    $tab = null;
    $tobj = array();
    array_push($tobj, array('o' => $conf, 'path' => $path));
    $tout = null;
    $q = null;
    $rgx = "/^\[(?P<exp>(.)+)\]$/i";
    while ($cq = array_pop($tobj)) {
        $g = explode("/", $cq['path']);
        $q = $cq["o"];
        $count = 0;
        foreach ($g as $k) {
            $count++;
            if (preg_match_all($rgx, trim($k), $tab) > 0) {
                $o = igk_conf_get_expression($tab["exp"][0]);

                $m = null;
                if (is_array($q)) {
                    foreach ($q as $s) {
                        $p = 1;
                        foreach ($o as $qt => $qs) {
                            $p = $p && igk_conf_match($s, $qt, $qs);
                            if (!$p)
                                break;
                        }
                        if ($p) {
                            //bind array
                            if ($m == null)
                                $m = $s;
                            else {
                                if (!is_array($m)) {
                                    $m = array($m);
                                }
                                $m[] = $s;
                            }
                        }
                    }
                    if ($m) {
                        if ($count < igk_count($g)) {
                            $cpath = implode("/", array_slice($g, $count));
                            if (is_array($m)) {
                                foreach ($m as $mk => $mv) {
                                    array_push($tobj, array("o" => $mv, "path" => $cpath));
                                }
                            } else {
                                array_push($tobj, array("o" => $m, "path" => $cpath));
                            }
                            $m = null;
                            $q = null;
                            break;
                        }
                        $q = $m;
                        continue;
                    } else {
                        if ($strict) {
                            return $default;
                        }
                    }
                    $q = igk_getv($q, 0);
                    continue;
                } else {
                    $p = 1;
                    foreach ($o as $qt => $qs) {
                        $p = $p && igk_conf_match($q, $qt, $qs);
                        if (!$p)
                            break;
                    }
                    if ($p)
                        $m = $q;
                    else {
                        if ($strict)
                            return $default;
                        $m = $q;
                    }
                }
                if ($m) {
                    $q = $m;
                    continue;
                }
                return $default;
            }
            $q = igk_getv($q, $k);
            if ($q == null)
                return $default;
        }
        if ($q) {
            if ($tout === null) {
                $tout = $q;
            } else if (!is_array($tout)) {
                $tout = array($tout);
                $tout[] = $q;
            }
        }
    }
    return $tout;
}


///<summary> get the current subdomain from uri</summary>
///<remark> The subdomain name is independant of configured domain uri</remark>
/**
 *  get the current subdomain from uri
 */
function igk_io_subdomain_uri_name($uri = null)
{
    return IGKSubDomainManager::SubDomainUriName($uri);
}


///<summary>get current domain from uri</summary>
/**
 * get current domain from uri
 */
function igk_io_domain_uri_name($uri = null)
{
    return IGKSubDomainManager::DomainUriName($uri);
}


///<summary>return the full request uri</summary>
/**
 * return the full request uri
 */
function igk_io_fullrequesturi()
{
    return igk_server()->full_request_uri;
}

function igk_io_handle_system_command()
{
    die(__METHOD__);
}

function igk_sys_handle_uri($uri = null)
{
    return RequestHandler::getInstance()->handle_uri($uri);
}

// ----------------------------------------
// + | request helper function 
// ----------------------------------------
///<summary></summary>
///<param name="tab"></param>
///<param name="key"></param>
///<param name="value" default="null"></param>
/**
 * 
 * @param mixed $tab
 * @param mixed $key
 * @param mixed|closure $value the default value is null
 */
function igk_getrequest($tab, $key, $value = null)
{
    if (is_object($key))
        return $value;
    if (isset($tab[$key])) {
        $t = $tab[$key];
        if (!is_array($t))
            return igk_str_quotes($t);
        return $t;
    }
    if (is_callable($value) && ($value instanceof Closure)) {
        return $value();
    }
    return $value;
}

///<summary> get request value</summary>
/**
 *  get request value
 */
function igk_getr($key, $value = null)
{
    return igk_getrequest($_REQUEST, $key, $value);
}

///<summary>get GET value</summary>
/**
 * get GET value
 */
function igk_getg($key, $value = null)
{
    return igk_getrequest($_GET, $key, $value);
}

///<summary>get a check POST value</summary>
/**
 * get a check POST value
 */
function igk_getp($key, $value = null)
{
    return igk_getrequest($_POST, $key, $value);
}
///<summary>get session param value</summary>
/**
 * get session param value
 */
function igk_gets($key, $value = null)
{
    return igk_getrequest($_SESSION, $key, $value);
}

///get request object value
/**
 */
function igk_getru($key, $value = null)
{
    if (is_object($key))
        return $value;
    if (isset($_REQUEST[$key]))
        return str_replace("-", "_", igk_str_quotes($_REQUEST[$key]));
    return $value;
}
///<summary>get the value between value and default. if $value is empty or null default</summary>
/**
 * get the value between value and default. if $value is empty or null default
 */
function igk_gettv($value, $default)
{
    if (($value == null) || empty($value))
        return $default;
    return $value;
}

/**
 * retrieve arguments helper
 * @param mixed $f
 */
function igk_io_arg_from($f)
{
    $arg = null;
    if (strstr($f, "/")) {
        $a = explode("/", $f);
        $f = $a[0];
        $b = array_slice($a, 1);
        if (igk_count($b) == 1) {
            $arg = $b[0];
        } else
            $arg = $b;
    }
    return $arg;
}

///<summary>return the controller registrated class</summary>
/**
 * return the controller registrated class
 */
function igk_sys_get_controller($n)
{
    return IGKControllerManagerObject::GetSystemController($n);
}
///<summary></summary>
///<param name="ctrlname"></param>
/**
 * 
 * @param mixed $ctrlname
 */
function igk_init_ctrl($ctrlname)
{
    return IGKControllerManagerObject::InitController($ctrlname);
}

///<summary>shortcut to string ::Format method helper</summary>
/**
 * shortcut to string ::Format method helper
 * @param string $data format key
 * @return string formatted string
 * @throws IGKException 
 */
function igk_str_format($data)
{
    return IGKString::Format(...func_get_args());
}


///<summary></summary>
///<param name="dirname"></param>
///<param name="mode" default="IGK_DEFAULT_FOLDER_MASK"></param>
/**
 * 
 * @param mixed $dirname
 * @param mixed $mode the default value is IGK_DEFAULT_FOLDER_MASK
 */
function igk_io_createdir($dirname, $mode = IGK_DEFAULT_FOLDER_MASK)
{
    return IO::CreateDir($dirname, $mode);
}


///<summary>count helper</summary>
///<param name="item"></param>
/**
 * count helper
 * @param mixed $item
 */
function igk_count($item)
{
    if (is_string($item))
        return strlen($item);
    if (is_array($item))
        return count($item);
    if (is_object($item)) {
        if ($item instanceof Countable){
            return $item->count();
        }
        if (method_exists(get_class($item), "getCount"))
            return $item->getCount();
        if (method_exists(get_class($item), "getRowCount"))
            return $item->getRowCount();
    }
    return 0;
}

///<summary></summary>
///<param name="f"></param>

///<summary>return the base request uri - start at $basedir</summary>
/**
 * return the base request uri - start at $basedir
 */
function igk_io_base_request_uri($rm_redirectvar = 1)
{
    $s = igk_io_baseuri();
    $d = igk_io_fullrequesturi();
    $o = '/' . ltrim(substr($d, strlen($s)), '/');
    ($rm_redirectvar) && igk_io_rm_redirectvar($o);
    return $o;
}


///<summary>remove redirected query var form query</summary>
/**
 * remove redirected query var form query
 */
function igk_io_rm_redirectvar(&$uri, $force = 0)
{
    if ($force || igk_server()->REDIRECT_STATUS == 200) {
        $g = parse_url($uri);
        $tab = array();
        if (isset($g["query"])) {
            parse_str($g["query"], $tab);
            // unset redirection variable
            foreach (array_keys($tab) as $k) {
                if (strpos($k, "__") === 0) {
                    unset($tab[$k]);
                }
            }
            unset($tab["__c"]);
            unset($tab["__e"]);
        }
        $uri = $g["path"];
        if (count($tab) > 0) {
            $uri .= "?" . http_build_query($tab);
        }
    }
}

function igk_createobj($tab = null)
{
    $o = new stdClass();
    if ($tab) {
        foreach ($tab as $k => $v) {
            $o->$k = $v;
        }
    }
    return $o;
}
///<summary></summary>
///<param name="n"></param>
/**
 * 
 * @param object $n
 */
function igk_is_class_incomplete($n)
{
    return get_class($n) === __PHP_Incomplete_Class::class;
}

function igk_realpath($p)
{
    return Path::getInstance()->realpath($p);
    // $o = "";
    // $path = igk_html_uri($path);
    // $offset = 0;
    // if ($o = realpath($path)){
    //     return $o;
    // }else {
    // 	//check if contains
    // 	$found = 0;
    //     while(($pos = strpos( $path,"../", $offset))!==false){
    // 		$found = 1;
    //         if (!($ch = realpath(substr($path, 0, $pos+3)))){
    //             return false;
    //         }
    //         $path = igk_html_uri($ch) ."/".substr($path, $pos+3);
    //         $offset =strlen($ch);
    //     }
    // 	if (!$found)
    // 		return null;
    // }
    // if (!$b){

    // }

    // while($c = preg_match("#(?P<n>(\.\.\/)+)#", $path, $tab, PREG_OFFSET_CAPTURE, $offset)){

    //     $d = $tab[0];
    //     if ($cc = igk_realpath(substr($path, 0, $noff = $d[1]+strlen($d[0])))){
    //         $path = igk_html_uri($cc)."/".substr($path, $noff);
    //     }else {
    //         $failed = 1;
    //         break;
    //     }
    // }
    // if (!$failed){
    //     $o = $path;
    // }
    // return $path;
}

///<summary>check if is sub directory</summary>
/**
 * check if is sub directory
 */
function igk_io_is_subdir($p, $c)
{
    return IO::IsSubDir($p, $c);
}

///<summary>return default configuration settings</summary>
/**
 * return default configuration settings
 */
function igk_sys_getdefaultctrlconf()
{
    return array(
        "clDataAdapterName" => IGK_CSV_DATAADAPTER,
        "clDataSchema" => false,
        "clDisplayName" => null,
        "clRegisterName" => null,
        "clParentCtrl" => null,
        "clTargetNodeIndex" => 0,
        "clVisiblePages" => "*",
        "clDescription" => null,
        "auto_cache_view"=>1
    );
}

function igk_sys_reflect_class($cl)
{
    static $reflection;
    if ($reflection === null) {
        $reflection = [];
    }
    if (is_object($cl)) {
        $cl = get_class($cl);
    }
    $reflection[$cl] = 1;
    return new ReflectionClass($cl);
}

/**
 * get working directory
 * @return void 
 */
function igk_io_workingdir(){
    if(defined( "IGK_WORKING_DIR")){
        return IGK_WORKING_DIR;
    }
    $app_dir = igk_io_applicationdir();
    $base_dir = igk_io_basedir();
    if ($app_dir == $base_dir){
        define("IGK_WORKING_DIR", $app_dir);
        return $app_dir;
    }
    $c = 0;
    while($app_dir && ($app_dir!="/")){
        $app_dir = dirname($app_dir);
        $c++;
        if ($c>10) break;
        if (strstr($base_dir, $app_dir)){
            define("IGK_WORKING_DIR", $app_dir);
            return $app_dir;
        }
    }
    die("failed to found working directory ".getcwd());
}

/**
 * application environment setting
 */
function igk_setting(){
    require_once IGK_LIB_CLASSES_DIR."/IGKEnvironmentSettings.php";
    return \IGK\IGKEnvironmentSettings::getInstance();
}