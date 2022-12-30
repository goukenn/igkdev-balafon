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

use IGK\ApplicationLoader;
use IGK\Resources\R;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Http\RequestHandler;
use IGK\System\IO\FileWriter as File;
use IGK\System\IO\FileWriter;
use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\Helper\StringUtility as IGKString;
use IGK\Helper\SysUtils;
use IGK\Manager\ApplicationControllerManager;
use IGK\Server;
use IGK\System\IO\Path;
use IGK\System\Regex\RegexConstant;

use function igk_resources_gets  as __;


///<summary>shortcut to get server info</summary>
/**
 * shortcut to get server info
 * @return Server server
 */
function igk_server()
{
    return Server::getInstance();
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

///<summary> encapsulate exit function. used for debugging purpose</summary>
/**
 *  helper: encapsulate exit function. used for debugging purpose
 *  @throws Exception
 *  @endcode exit
 */
function igk_exit($close = 1)
{
    if (igk_environment()->isAJXDemand) {
        igk_hook(IGKEvents::HOOK_AJX_END_RESPONSE, []);
        igk_environment()->isAJXDemand = null;
    }
    if ($close && !empty(session_id())) {
        igk_hook(IGKEvents::ON_BEFORE_EXIT, array(igk_app(), null)); 
    }
    exit;
}
/**
 * helper: session write close 
 */
function igk_sess_write_close()
{
    if (igk_environment()->isDev()) {
        igk_ilog("close session " . session_id());
        igk_ilog(igk_ob_get_func(function () {
            igk_trace(2, 0, 0, 0, 1);
        }));
    } 
    return @session_write_close();
}
///<summary>get output type</summary>
/**
 * get zip output type
 */
function igk_zip_output_type($forcegzip = 0)
{
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
/**
 * zip content and output
 * @param string $c content to string 
 * @param int|bool $forcegzip forcing gzip - no dectection 
 * @param int|bool $header write header 
 * @param mixed $type 
 * @return void 
 * @throws IGKException 
 */
function igk_zip_output(string $c, int $forcegzip = 0, $header = 1, &$type = null)
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
 * helper: shortcut to resource get __
 * @param string|array<string> $text formatted key
 * @param string|null $default default value
 */
function igk_resources_gets($text, $default = null)
{
    $args = func_get_args();
    if (is_array($text)) {
        $m = array_slice($args, 1);
        // $m = array_fill_keys(array_keys($m), $m);
        $text = implode('', array_filter(array_map(function ($a) use ($m) {
            return igk_resource_gets_map($a, $m);
        }, $text)));
        $args[0] = $text;
    }
    return call_user_func_array(array(R::class, 'Gets'), $args);
}
/**
 * helper: resource map string
 * @param null|string $a 
 * @param array $args 
 * @return mixed 
 */
function igk_resource_gets_map(?string $a, array $args)
{
    if (igk_is_null_or_empty($a) || empty(($a = trim($a)))) {
        return $a;
    }
    array_unshift($args, $a);
    return call_user_func_array(array(R::class, 'Gets'), $args);
}
if (!function_exists('igk_getv')) {
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
}
if (!function_exists('igk_getv_nil')) {
    ///<summary>helper : get value or nil if empty</summary>
    ///<param name="default"helper : get value or nil if empty</param>
    /**
     * helper : get value or nil if empty
     * @param mixed default value or callback expression
     */
    function igk_getv_nil($array, $key, $default = null)
    {
        return empty($c = igk_getpv($array, array($key), $default)) ? null : $c;
    }
}


/**
 * from laravel helper get request object 
 * @param mixed $ob 
 * @param callable|null $callback 
 * @return mixed 
 */
function  igk_geto($ob, string $name, callable $callback = null)
{
    $t = igk_getv($ob, $name);
    if (is_null($callback)) {
        return new IGKObjStorage($t);
    } else if (!is_null($t)) {
        return $callback($t);
    }
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
    if ((is_null($array)) || (empty($key) && ($key !== 0))) {
        return $default;
    }
    if (is_null($key)) {
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
    if (is_null($o)) {
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
    return ApplicationLoader::getInstance()->registerLoading($name, $entryNS, $classdir, $refile);
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
    if (is_null($obj)) {
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
 * helper : append query helper 
 * @param string $uri 
 * @param mixed $param 
 * @return string 
 */
function igk_io_append_query(string $uri, $param)
{
    $uri_info = parse_url($uri);
    $query = [];
    if (isset($uri_info['query'])) {
        parse_str($uri_info['query'], $query);
    }
    if (is_string($param)) {
        $q = [];
        parse_str($param, $q);
        $query = array_merge($query, $q);
    } else {
        $query = array_merge($query, $param);
    }

    $s = !empty($query) ?  http_build_query($query) : "";
    if ($s)
        return explode("?", $uri)[0] . '?' . $s;
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
/**
 * return system path
 * @param mixed $relativepath 
 * @return string|string[]|null<b>|null 
 */
function igk_io_syspath($relativepath = null)
{
    if ($relativepath)
        return igk_dir(igk_io_applicationdir() . "/" . $relativepath);
    return igk_io_applicationdir();
}
///<summary>get application directory</summary>
/**
 * get application directory
 * @return string
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
    if (isset($_SERVER["SERVER_PROTOCOL"])) {
        return false;
    }
    return ((isset($_SERVER["argv"]) && !isset($_SERVER["SERVER_PROTOCOL"]))) || igk_environment()->get("sys://func/" . __FUNCTION__);
}
function igk_set_cmd($v = 1)
{
    igk_environment()->set("sys://func/igk_is_cmd", $v);
}
/**
 * helper: is null or empty
 * @param mixed $c 
 * @return bool 
 */
function igk_is_null_or_empty($c)
{
    return (is_null($c)) || empty($c);
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
    if (is_null($inUse)) {
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
    if (igk_sys_zip_core($tfile, false)) {
        if ($download)
            igk_download_file("Balafon." . IGK_VERSION . ".zip", $tfile, "binary", 0);
        return $tfile;
    }
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
 * pre print_r helper
 * @param mixed $p
 */
function igk_wl_pre($p)
{
    echo "<pre>";
    print_r($p);
    echo "</pre>";
}
/**
 * pre var_dump helper
 * @param mixed $p 
 * @return void 
 */
function igk_dump_pre($p)
{
    echo "<pre>";
    var_dump($p);
    echo "</pre>";
}
function igk_dev_wln()
{
    if (igk_environment()->isDev()) {
        call_user_func_array("igk_wln", func_get_args());
    }
}
function igk_dev_ilog()
{
    if (igk_environment()->isDev()) {
        call_user_func_array("igk_ilog", func_get_args());
    }
}
function igk_dev_wln_e()
{
    if (igk_environment()->isDev()) {
        call_user_func_array("igk_wln_e", func_get_args());
        igk_exit();
    }
}



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
                $r2 .= '<td>';

                $r2 .= is_array($v) ? json_encode($v) : $v;
                $r2 .= '</td>';
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
    // igk_trace();
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
    if (is_null($lf)) {
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
    // igk_trace();
    // ;exit;
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
/**
 * helper: shortcut to IGKSysUtil::Encrypt method
 * @param mixed $data 
 * @param mixed $prefix 
 * @return string|false 
 */
function igk_encrypt($data, $prefix = null)
{
    return IGKSysUtil::Encrypt($data, $prefix);
}
function igk_sys_copyright()
{
    return "IGKDEV &copy; 2011-" . date('Y') . " " . __("all rights reserved");
}

/**
 * trace utility in buffer
 * @return string|false 
 */
function igk_ob_trace($depth = 0, $sep = "", $count = -1, $header = 0)
{
    return igk_ob_get_func('igk_trace', [2 + $depth, $sep, $count, $header]);
}
///<summary></summary>
///<param name="depth"></param>
/**
 * 
 * @param mixed $depth the default value is 0
 */
function igk_trace($depth = 0, $sep = "", $count = -1, $header = 0, ?bool $cmd = null)
{
    $callers = debug_backtrace();
    $o = "";
    $tc = 1;
    $cmd = $cmd ?? igk_is_cmd();

    if ($cmd) {
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
        //$o .= "<th>" . __("Line") . "</th>";
        $o .= "<th>" . __("File") . "</th>";
        $o .= "<th>" . __("Function") . "</th>";
        $o .= "<th>" . __("In") . "</th>";
        $o .= "</tr>" . $sep;
    }
    $_base_path = !igk_environment()->isDev() && defined("IGK_BASE_DIR");

    for ($i = $depth; $i < count($callers); $i++, $tc++) {


        $f = igk_getv($callers[$i], "function");
        $c = igk_getv($callers[$i], "class", "__global");
        $o .= "<tr style=\"background-color: " . $colors[$i % 2] . "; border-bottom: 2px solid #f3f3f3; \">";
        $o .= "<td style=\"{$tds}\">" . $tc . "</td>";
        $ln = igk_getv($callers[$i], "line");
        //$o .= "<td style=\"{$tds}\">" . $ln . "</td>";
        $o .= "<td style=\"{$tds}\" class=\"clip_click\" >";
        $g = igk_getv($callers[$i], "file");
        if ($_base_path && $g) {
            $g = igk_io_collapse_path($g);
        }
        $o .= $g;
        $o .= ":" . $ln;
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
    $o .= "<script type=\"text/javascript\"> var ct = document.querySelectorAll('.clip_click').forEach(function(i) { i.addEventListener('click', function() { window.getSelection().selectAllChildren(this);});}); </script>";
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

///<summary>get system directory presentation shortcut</summary>
/**
 * get system directory presentation shortcut
 * @return string|null 
 */
function igk_dir($dir, $separator = DIRECTORY_SEPARATOR)
{
    return IO::GetDir($dir, $separator);
}
/**
 * prepare component storage
 * @return object 
 */
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
 * helper: get if the system is on production mode
 */
function igk_sys_env_production()
{
    return igk_environment()->isOPS();
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
 * @param null|array|object|\IGK\IHookOptions $options to pass default|output|type
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
function igk_reg_hook($name, $callback, $priority = 10, $injectable = true)
{
    IGKEvents::reg_hook($name, $callback, $priority, $injectable);
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
/**
 * helper to get configuration
 * @return \IGK\System\Configuration\ConfigData|\IGK\System\Configuration\ISysConfigurationData
 */
function igk_configs()
{
    return IGKAppConfig::getInstance()->getData();
}

/**
 * helper to get library configuration
 * @return IGK\System\Configuration\ControllerConfigurationData
 */
function igk_lib_configs()
{
    return IGK\System\Configuration\Controllers\ConfigureController::ctrl()->getConfigs();
}
///<summary>shortcut to get controller by ref_name</summary>
/**
 * shortcut to get controller application controller by ref_name
 * @param string $name reference name. key or class name
 * @param int|bool $throwex throw exception if not found
 * @return null|IGK\Controllers\BaseController controller found
 */
function igk_getctrl(string $name, $throwex = 1)
{
    return  igk_app()->getControllerManager()->getController($name, $throwex);
}
/**
 * shortcut to write log
 * @param array|string $message 
 * @param string|null $tag 
 * @param mixed $traceindex tracing index
 * @return void 
 * @throws IGKException 
 */
function igk_ilog($message, ?string $tag = null, $traceindex = 0, $dblog = true)
{

    IGKLog::Append($message, $tag, $traceindex, $dblog);
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
 * retrieve 
 * @param mixed $file
 * @param mixed $content
 * @param mixed $overwrite the default value is true
 * @param mixed $chmod the default value is IGK_DEFAULT_FILE_MASK
 * @param mixed $type the default value is "w+"
 */
function igk_io_w2file($file, $content, $overwrite = true, $chmod = IGK_DEFAULT_FILE_MASK, $type = "w+")
{
    // igk_debug_wln("try create : ".$file."\n");
    return File::Save($file, $content, $overwrite, $chmod, $type);
}
/**
 * get defaulf webpage controller
 * @return null|BaseController  
 * @throws IGKException 
 */
function igk_get_defaultwebpagectrl()
{
    return igk_app()->getControllerManager()->getDefaultController()
        ?? igk_getctrl(igk_configs()->get("default_controller") ?? "", false);
}

///<summary>get object with igk Xpath selection model</summary>
/**
 * get object with igk Xpath selection model
 * syntaxe : 
 */
function igk_conf_get($conf, $path, $default = null, $strict = 0)
{
    // + | --------------------------------------------------------------------
    // + | xpath description definition - helper 
    // + | for every item in conf that match the rule get a value. 
    // + | 
    // + | 
    // + | 


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
            if (is_null($tout)) {
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

/**
 * handle system command
 * @param string $uri 
 * @return bool 
 * @throws IGKException 
 */
function igk_io_handle_system_command(string $uri): bool
{
    $ctrl_check = "(" . RegexConstant::GUID_CHECK . "|" . IGK_FQN_NS_RX . ")";
    $rx = "#^(" . igk_io_baseuri() . ")?\/!@(?P<type>" . IGK_IDENTIFIER_RX . ")\/(\/)?(?P<ctrl>" . $ctrl_check . ")\/(?P<function>" . IGK_IDENTIFIER_RX . ")(\/(?P<args>(.)*))?$#i";
    $c = preg_match_all($rx, explode("?", $uri)[0], $ctab);
    if ($c > 0) {
        if ($guid = igk_getv($ctab['guid'], 0)) {
            if ($ctab["ctrl"][0] == $guid) {
                $ctab["ctrl"][0] = '{' . $guid . '}';
            }
        }
        igk_getctrl(IGK_SYSACTION_CTRL)->invokePageAction($ctab["type"][0], $ctab["ctrl"][0], $ctab["function"][0], explode("?", $ctab["args"][0])[0]);
        return true;
    }
    return false;
}
/**
 * helper : handle request uri
 * @param ?string|mixed $uri request string or request object  
 * @return void 
 * @throws IGKException 
 */
function igk_sys_handle_uri($uri = null)
{
    return RequestHandler::getInstance()->handle_uri($uri);
}


///<summary>load library specification</summary>
/**
 * load library specification
 * @param string $dir directory to load
 * @param string $ext extension file or regext to used for matching class
 * @param string $excludedir directory to exclude
 * @return null|array $files loaded
 * */
function igk_loadlib(string $dir, string $ext = ".php", ?array $excludedir = null)
{
    $sdir = is_dir($dir) ? $dir : igk_dir(igk_realpath($dir));
    if (empty($sdir)) {
        return;
    }
    
    IGK\System\Diagnostics\Benchmark::mark(__FUNCTION__);
    $dir = $sdir;
    $dirs = array($dir);
    $files = array();
    $excluded_key = IGKEnvironment::IGNORE_LIB_DIR;
    $excludedir = $excludedir ?? array_merge(igk_get_env($excluded_key) ?? [], igk_default_ignore_lib());
    $ln  = strlen($ext);
    if (!$excludedir)
        $excludedir = array();
    $m = &$excludedir;
    igk_environment()->set($excluded_key,  $m);
    $loadeds = [];
    while (igk_count($dirs) > 0) {
        $dir = realpath(array_shift($dirs));
        if (isset($excludedir[$dir]))
            continue;
        $hdir = @opendir($dir);
        if (!$hdir)
            continue;
        $file = IGK_STR_EMPTY;
        // inlude .global.php first 
        if (is_file($gdir = $dir . "/.global.php")) {
            include_once($gdir);
            $files[] = igk_uri($gdir);
            $loadeds[$gdir] = 1;
            if (isset(igk_environment()->{$excluded_key}[$dir])) {
                closedir($hdir);
                continue;
            }
        }

        while ($fdir = readdir($hdir)) {
            $excludedir = igk_environment()->{$excluded_key};
            if (($fdir == ".") || ($fdir == "..") || isset($excludedir[$fdir]))
                continue;
            $file = $dir . DIRECTORY_SEPARATOR . $fdir;
            if (is_dir($file)) {
                if (isset($excludedir[$file]) || ($fdir[0] == ".")) {
                    $excludedir[$file] = 1;
                    continue;
                }
                $dirs[] = $file;
            } else {
                if (isset($loadeds[$file]))
                    continue;
                if (strstr($file, "." . IGK_DEFAULT_VIEW_EXT) || !strpos($file, $ext, -$ln))
                    continue;
                include_once($file);
                $files[] = igk_uri($file);
                $loadeds[$file] = 1;
            }
        }
        closedir($hdir);
        if (count($dirs) > 1) {
            sort($dirs);
        }
    }
    return $files;
}

// ----------------------------------------
// + | request helper function 
// ----------------------------------------
///<summary></summary>
///<param name="tab"></param>
///<param name="key"></param>
///<param name="value" default="null"></param>
/**
 * retrieve from objet or table 
 * @param mixed $tab table to check
 * @param mixed $key value path to get
 * @param mixed|closure $value default value
 * @return mixed|null founded value or default 
 */
function igk_get_tab_value($tab, $key, $value = null)
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

if (!function_exists('igk_getr')) {
    ///<summary> get request value</summary>
    /**
     *  get request value
     */
    function igk_getr($key, $value = null)
    {
        return igk_get_tab_value($_REQUEST, $key, $value);
    }
}

///<summary>get GET value</summary>
/**
 * get GET value
 */
function igk_getg($key, $value = null)
{
    return igk_get_tab_value($_GET, $key, $value);
}

///<summary>get a check POST value</summary>
/**
 * get a check POST value
 */
function igk_getp($key, $value = null)
{
    return igk_get_tab_value($_POST, $key, $value);
}
///<summary>get session param value</summary>
/**
 * get session param value
 */
function igk_gets($key, $value = null)
{
    return igk_get_tab_value($_SESSION, $key, $value);
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
        if ($item instanceof Countable) {
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
/**
 * create and fill stdClass from array or object
 * @param mixed $tab 
 * @return stdClass|mixed 
 */
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
///<summary>get realpath helper</summary>
/**
 * get realpah helper
 * @param mixed $p 
 * @return string|false|null 
 * @throws IGKException 
 */
function igk_realpath(string $p)
{
    return Path::getInstance()->realpath($p);
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
        "clVersion" => "1.0",
        "clDataAdapterName" => igk_configs()->get("default_dataadapter", IGK_CSV_DATAADAPTER),
        IGK_CTRL_CNF_USE_DATASCHEMA => false,
        "clDisplayName" => null,
        "clRegisterName" => null,   // register name will be converted as entry namespace
        "clParentCtrl" => null,
        "clTargetNodeIndex" => 0,
        "clVisiblePages" => "*",
        "clDescription" => null,
    );
}

function igk_sys_reflect_class($cl)
{
    static $reflection;
    if (is_null($reflection)) {
        $reflection = [];
    }

    if (is_object($cl)) {
        $cl = get_class($cl);
    }
    if (isset($reflection[$cl])) {
        return $reflection[$cl];
    }
    if (is_string($cl) && class_exists($cl)) {
        $rf = new ReflectionClass($cl);
        $reflection[$cl] = $rf;
        return $rf;
    }
    igk_wln_e("missing." . $cl, \com\igkdev\app\llvGStock\MappingService::class == $cl, class_exists($cl), class_exists(\com\igkdev\app\llvGStock\MappingService::class));
}

/**
 * get working directory
 * @return void 
 */
function igk_io_workingdir()
{
    if (defined("IGK_WORKING_DIR")) {
        return IGK_WORKING_DIR;
    }
    $app_dir = igk_io_applicationdir();
    $base_dir = igk_io_basedir();
    if ($app_dir == $base_dir) {
        define("IGK_WORKING_DIR", $app_dir);
        return $app_dir;
    }
    $c = 0;
    while ($app_dir && ($app_dir != "/")) {
        $app_dir = dirname($app_dir);
        $c++;
        if ($c > 10) break;
        if (strstr($base_dir, $app_dir)) {
            define("IGK_WORKING_DIR", $app_dir);
            return $app_dir;
        }
    }
    if (IGKApp::IsInit()){
        define('IGK_WORKING_DIR', $dir= getcwd());
        return $dir;
    }
    die("failed to found working directory " . getcwd());
}

/**
 * application environment setting
 * @return \IGK\EnvironmentSettings environment setting
 */
function igk_setting()
{
    require_once IGK_LIB_CLASSES_DIR . "/IGKEnvironmentSettings.php";
    return \IGK\IGKEnvironmentSettings::getInstance();
}
/**
 * write text on testing
 * @return void 
 * @throws IGKException 
 */
function igk_test_wln()
{
    if (defined("IGK_TEST_INIT")) {
        igk_wln(...func_get_args());
    }
}
/**
 * get or portion of script code
 * @param mixed $file 
 * @param mixed $start_line 
 * @param mixed $end_line 
 * @return string 
 */
function igk_get_script_code($file, $start_line, $end_line = null)
{
    $src = explode("\n", file_get_contents($file));
    return implode("\n", array_slice($src, $start_line, $end_line ? abs($start_line - $end_line) : null));
}


///<summary>return an array of default sys ignored folder keys</summary>
/**
 * return an array of default sys ignored folder keys
 */
function igk_default_ignore_lib($dir = null)
{
    $tk = array(
        IGK_LIB_FOLDER => 1,
        IGK_CONF_FOLDER => 1,
        IGK_DATA_FOLDER => 1,
        IGK_VIEW_FOLDER => 1,
        IGK_CONTENT_FOLDER => 1,
        IGK_SCRIPT_FOLDER => 1,
        IGK_STYLE_FOLDER => 1,
        IGK_ARTICLES_FOLDER => 1,
        IGK_CGI_BIN_FOLDER => 1,
    );
    if ($dir) {
        $keys = array_keys($tk);
        foreach ($keys as $m) {
            $tk[igk_uri($dir . '/' . $m)] = 1;
        }
    }
    return $tk;
}

///<summary>convert system path to uri scheme</summary>
/**
 * shorcut string as uri path 
 * @param string $u path to convert
 * */
function igk_uri(string $u): string
{
    return IGKString::Uri($u);
}



///<summary>check if $c is a framework callback object</summary>
///<param name="$c">the callback object to check</param>
/**
 * check if $c is a framework callback object
 * @param mixed $$c the callback object to check
 */
function igk_is_callback_obj($c)
{
    $s = IGK_OBJ_TYPE_FD;
    return (is_array($c) && isset($c[$s]) && ($c[$s] == "_callback")) || (is_object($c) && !is_callable($c) && isset($c->$s) && ($c->$s == "_callback"));
}


///<summary>call it to ignore a specific directory on javascript loading process</summary>
///<param name="dir">if dir is null or not an existing directory, return the current directory list</param>
/**
 * call it to ignore a specific directory on javascript loading process
 * @param mixed $dir if dir is null or not an existing directory, return the current directory list
 */
function igk_sys_js_ignore($dir = null)
{
    $v_key = IGKEnvironmentConstants::IGNORE_JS_DIR;
    $d = igk_get_env($v_key);
    if (($dir === null) || !is_dir($dir))
        return $d;
    if (!$d) {
        $d = array();
    }
    $d[igk_uri($dir)] = 1;
    igk_set_env($v_key, $d);
    return $d;
}

///<summary>register global balafon settings</summary>
/**
 * register global balafon settings
 */
function igk_reg_global_setting($n, $d, $desc = null)
{
    $k = IGK_ENV_GLOBAL_SETTING;
    $tab = igk_get_env($k, array());
    if (isset($tab[$n]))
        return 0;
    $obj = igk_createobj();
    $obj->clName = $n;
    $obj->clData = $d;
    $obj->clDesc = $desc;
    $tab[$n] = $obj;
    igk_set_env($k, $tab);

    // if (igk_current_context() != IGKAppContext::initializing) {
    //     if (igk_app()->IsInit() && !isset(igk_configs()->{$n})) {
    //         igk_configs()->{$n} = $d;
    //     }
    // }
    return 1;
}
///<summary>load environment files</summary>
///<param name='dirname' type='string'>base directory</param>
///<param name='tab' type='array'>list of directory . relative to dirname or absolute path </param>
///<return type='array'>loaded files</param>
/**
 * load environment files
 * @param string $dirname
 * @param array $tab list of folder to load. if relative to dirname or absolute paht
 * @return array loaded files
 */
function igk_load_env_files($dirname, $tab = [IGK_INC_FOLDER, IGK_PROJECTS_FOLDER])
{
    $t_files = array();
    igk_hook("sys://event/cachelibreload", array(null, (object)array("files" => &$t_files)));
    $tab = $tab == null ? array(IGK_INC_FOLDER, IGK_PROJECTS_FOLDER) : $tab;
    $bckdir = getcwd();
    chdir($dirname);

    while (($s = array_shift($tab)) !== null) {
        $dir = $s;
        if (!is_dir($s))
            $dir = $dirname . "/" . $s;
        $g_files = igk_loadlib($dir);
        if (is_array($g_files))
            $t_files = array_merge($t_files, $g_files);
    }
    chdir($bckdir);
    return $t_files;
}
/**
 * helper get php core version string 
 * @return string string version
 */
function igk_php_sversion(?string $version = PHP_VERSION): string
{
    if (is_null($version)) {
        $version = PHP_VERSION;
    }
    $version = preg_split("/[^0-9\.]/i", $version)[0];
    return implode('.', array_slice(explode('.', $version), 0, 2));
}

///<summary></summary>
///<param name="code"></param>
///<param name="message" default=""></param>
/**
 * 
 * @param mixed $code response mesage code
 * @param mixed $message custom message to add to response
 * @param array headers list of extra header entries
 */
function igk_set_header($code, $message = "", $headers = [])
{
    if (igk_is_cmd() || headers_sent())
        return false;
    // igk_wln_e('need ->headers', igk_is_cmd(),  headers_sent());
    static $fcall = null;
    if ($fcall === null)
        $fcall = 0;
    $message = trim($message);
    if (!empty($message))
        $message = ";" . $message;
    $message .= ";" . IGK_FRAMEWORK . ": " . IGK_CODE_NAME . " - " . IGK_VERSION;
    $h = igk_get_allheaders();
    $new = 1;
    if (($o = igk_getv($h, "ORIGIN")) && ($ref = igk_getv($h, "REFERER"))) {
        if (rtrim($o, "/") == rtrim($ref, "/")) {
            $new = 0;
        }
    }
    $msg = igk_get_header_status($code);
    $txt = "Status: {$code} $msg";
    if (!$fcall) {
        if ($new) {
            header($msg);
            header($txt, 1);
            header(IGK_FRAMEWORK . ":" . IGK_CODE_NAME . "-" . IGK_VERSION);
        }
    } else {
        header($txt, 1, $code);
    }
    igk_environment()->isDev() && header("srv-msg:" . $message);
    if ($headers)
        foreach ($headers as $k) {
            header($k);
        }
    $fcall = 1;
}

///<summary>bind my how header</summary>
/**
 * bind my how header
 */
function igk_get_allheaders()
{
    return igk_get_env(__FUNCTION__, function () {
        $tab = array();
        if (function_exists("getallheaders")) {
            $t = getallheaders();
            foreach ($t as $k => $v) {
                $k = strtoupper(str_replace('-', '_', $k));
                $tab[$k] = $v;
            }
        }
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $name = str_replace(' ', '-', substr($name, 5));
                $tab[$name] = $value;
            } else if ($name == "CONTENT_TYPE") {
                $tab["Content-Type"] = $value;
            } else if ($name == "CONTENT_LENGTH") {
                $tab["Content-Length"] = $value;
            }
        }
        return $tab;
    });
}
///<summary>Represente igk_get_header_status function</summary>
///<param name="code"></param>
/**
 * Represente igk_get_header_status function
 * @param mixed $code 
 */
function igk_get_header_status($code)
{
    return \IGK\System\Http\StatusCode::GetStatus($code);
}

///<summary>specifu cache output</summary>
/**
 * specifu cache output
 */
function igk_header_cache_output($second = 3600)
{
    $ts = gmdate("D, d M Y H:i:s", time() + $second) . " GMT";
    header("Expires: {$ts}");
    header("Pragma: cache");
    header("Cache-Control: max-age={$second}, public");
}

/**
 * full fill data with 
 */
function igk_full_fill(&$sdata, $tdata)
{
    if (is_array($sdata)) {
        foreach ($sdata as $k => $v) {
            $data[$k] = igk_getv($tdata, $k, $v);
        }
        return;
    }
    foreach ($sdata as $k => $v) {
        $sdata->$k = igk_getv($tdata, $k, $v);
    }
}
