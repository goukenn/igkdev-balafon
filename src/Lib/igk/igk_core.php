<?php
// @file: igk_core.php
// @author: C.A.D. BONDJE DOUE
// @description: core function and initialiation
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

defined("IGK_FRAMEWORK") || die("REQUIRE FRAMEWORK - No direct access allowed");

use IGK\ApplicationLoader;
use IGK\Constants;
use IGK\Resources\R;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Http\RequestHandler;
use IGK\System\IO\FileWriter as File;
use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\Helper\StringUtility as stringUtility;
use IGK\Helper\SysUtils;
use IGK\Helper\TraitHelper;
use IGK\Server;
use IGK\System\IArrayKeyExists;
use IGK\System\IO\Path;
use IGK\System\Number;
use IGK\System\Regex\RegexConstant;
use function igk_resources_gets  as __;

/**
 * shortcut to get server info
 * @return Server server
 */
function igk_server()
{
    return Server::getInstance();
}
if (!function_exists('igk_environment')) {
    /**
     * shortcut get core environment 
     * @return IGKEnvironment environment
     */
    function igk_environment()
    {
        return IGKEnvironment::getInstance();
    }
}
/**
* helper: encapsulate exit function. used for debugging purpose
* @param int $close
* @param int $status
* @throws Exception
* @return mixed
*/
function igk_exit(int $close = 1, int $status = 0)
{
    if (igk_environment()->isAJXDemand) {
        igk_hook(IGKEvents::HOOK_AJX_END_RESPONSE, []);
        igk_environment()->isAJXDemand = null;
    }
    if ($close && !empty(session_id())) {
        igk_hook(IGKEvents::ON_BEFORE_EXIT, array(igk_app(), null));
    }
    exit($status);
}
/**
* helper: session write close
* @return mixed
*/
function igk_sess_write_close()
{
    /// DEBUG: Track close SESSION
    if (igk_environment()->isDev()) {
        $tab = $_SESSION ?? [];
        $closure = false;
        $object = [];
        while (count($tab) > 0) {
            $q = array_shift($tab);
            if ($q instanceof Closure) {
                $closure = true;
                break;
            }
            if (is_array($q) || is_object($q)) {
                if (is_object($q)) {
                    $object[get_class($q)] = 1;
                }
                foreach ($q as $m) {
                    if ($m instanceof Closure) {
                        $closure = true;
                        break 2;
                    }
                    if (is_array($m) || is_object($m)) {
                        array_unshift($tab, $m);
                    }
                }
            }
        }
        if ($closure) {
            igk_wln_e("can't serialize closure detected");
        }
    }
    try {
        return @session_write_close();
    } catch (Exception $e) {
        igk_ilog('Caught exception: ',  $e->getMessage(), "\n");
    }
}
/**
* get zip output type
* @param mixed $forcegzip
* @return mixed
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
/**
* zip content and output
* @param string $c content to string
* @param int|bool $forcegzip forcing gzip - no dectection
* @param int|bool $header write header
* @param mixed & $type
* @throws IGKException
* @return void
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
/**
* Igk die s.
* @param string $msg
* @param mixed ...$params
* @return mixed
*/
function igk_die_s(string $msg, ...$params)
{
    igk_die(sprintf(__($msg), ...$params));
}
/**
* die with message
* @param mixed $msg value. error_array|string.
* @param mixed $throwex bool throw exception
* @param mixed $code
* @throws Exception
* @return mixed
*/
function igk_die($msg = IGK_DIE_DEFAULT_MSG, $throwex = 1, $code = 500)
{
    if (defined('IGK_TRACE'))
        igk_trace();
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
        !defined('IGK_TEST_INIT') && igk_is_debug() && error_log(sprintf('%s - %s', '[BLF_EX]', $msg));
        // + | Last Exception   
      
        throw new IGKException($msg, $code);
    } else {
        ob_get_level() && ob_clean();
        igk_set_header($code);
        igk_dev_wln($msg . PHP_EOL);
        igk_exit();
    }
}
if (!function_exists('igk_die_exception')) {
    /**
    * Igk die exception.
    * @param string $exception_class_name
    * @param null|string $msg
    * @param mixed $throwex
    * @param mixed $code
    * @return mixed
    */
    function igk_die_exception(string $exception_class_name, ?string $msg, $throwex = 1, $code = 500)
    {
        if (class_exists($exception_class_name)) {
            throw new $exception_class_name($msg, $code);
        }
        igk_die($msg, $throwex, $code);
    }
}
if (!function_exists('igk_resources_gets')) {
    /**
    * helper: shortcut to resource string dictionary get __
    * @param string|array<string> $text formatted key
    * @param string|null ...$parameters parameter
    * @return mixed
    */
    function igk_resources_gets($text, $parameters = null)
    {
        $args = func_get_args();
        if (is_array($text)) {
            $m = array_slice($args, 1);
            $text = implode('', array_filter(array_map(function ($a) use ($m) {
                return igk_resource_gets_map($a, $m);
            }, $text)));
            $args[0] = $text;
        }
        if ($g = R::GetStringResourceHandler()) {
            return call_user_func_array($g, $args);
        }
        return call_user_func_array(array(R::class, 'Gets'), $args);
    }
}
if (!function_exists('igk_resources_getsf')) {
    /**
     * shorcut helper 
     * @return mixed 
     */
    function igk_resources_getsf()
    {
        return call_user_func_array('igk_resources_sprintf', func_get_args());
    }
}
if (!function_exists('igk_resources_sprintf')) {
    /**
    * auto generate doc.
    * @param string $a
    * @param mixed ...$args
    * @return string
    */
    function igk_resources_sprintf(string $a, ...$args)
    {
        return sprintf(igk_resources_gets($a), ...$args);
    }
}
if (!function_exists('igk_resource_gets_map')) {
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
}
if (!function_exists('igk_getv')) {
    /**
    * get value in array
    * @param mixed $array
    * @param mixed $key
    * @param mixed $default
    * @return mixed
    */
    function igk_getv($array, $key, $default = null)
    {
        return igk_getpv($array, array($key), $default);
    }
}
if (!function_exists('igk_getchainv')) {
    /**
     * retrieve first value key chain 
     * @param mixed $n 
     * @param string $keys 
     * @param mixed $default 
     * @return mixed|mixed 
     * @throws Exception 
     */
    function igk_getchainv($n, string $keys, $default = null)
    {
        if (is_null($n) || !(is_array($n) || is_object($n))) {
            return $default;
        }
        $r = explode('|', $keys);
        while (count($r) > 0) {
            $q = array_shift($r);
            if (is_array($n) && !key_exists($q, $n)) {
                continue;
            }
            if (is_object($n) && !property_exists($n, $q)) {
                continue;
            }
            return igk_getv($n, $q, $default);
        }
        return $default;
    }
}
if (!function_exists('igk_in')) {
    /**
     * check for key presence in object or array
     * @param mixed $obj 
     * @param mixed $key 
     * @return bool 
     */
    function igk_in($obj, $key)
    {
        if (is_object($obj)) {
            return property_exists($obj, $key);
        }
        if (is_array($obj)) {
            return key_exists($key, $obj);
        }
        return false;
    }
}
if (!function_exists('igk_getv_nil')) {
    /**
    * helper : get value or nil if empty
    * @param mixed $array
    * @param mixed $key
    * @param mixed $default
    * @return mixed
    */
    function igk_getv_nil($array, $key, $default = null)
    {
        return empty($c = igk_getpv($array, array($key), $default)) ? null : $c;
    }
}
if (!function_exists('igk_unset')) {
    /**
    * Igk unset.
    * @param mixed & $o
    * @param mixed $k
    * @return mixed
    */
    function igk_unset(&$o, $k)
    {
        if (is_array($o)) unset($o[$k]);
        else if (is_object($o)) {
            unset($o->$k);
        }
    }
}
if (!function_exists('igk_getv_closure')) {
    /**
     * invoke from closure
     * @param mixed $data 
     * @param mixed $key 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    function igk_getv_closure($data, $key, $default = null)
    {
        $r = igk_getv($data, $key, $default);
        if ($r && igk_is_closure($r)) {
            return $r($key, $data, $default);
        }
        return $r;
    }
}
if (!function_exists('igk_getv_isset')) {
    /**
     * su
     * @param mixed $ob 
     * @param string $name 
     * @param mixed $default 
     * @return mixed 
     */
    function igk_getv_isset($ob, string $name, $default = null)
    {
        if (is_object($ob)) {
            if (isset($ob->$name)) {
                return $ob->$name ?? $default;
            }
        } else if (is_array($ob)) {
            if (isset($ob[$name])) {
                return $ob[$name];
            }
        }
        return $default;
    }
}
if (!function_exists('igk_geto')) {
    /**
    * from laravel helper get request object
    * @param mixed $ob
    * @param string $name
    * @param callable|null $callback
    * @return mixed
    */
    function  igk_geto($ob, string $name, ?callable $callback = null)
    {
        $t = igk_getv($ob, $name);
        if (is_null($callback)) {
            return new IGKObjStorage($t);
        } else if (!is_null($t)) {
            return $callback($t);
        }
    }
}
/**
* auto generate doc.
* @param mixed $array
* @param mixed $key
* @param mixed $default the default value is null
* @return mixed
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
    while ($array && (($q = array_shift($n)) || Number::IsZeroIndexNumber($q))) {
        $o = null;
        $ckey = $q;
        if (is_array($array) && isset($array[$q])) {
            $o = $array[$q];
        } else if (is_object($array)) {
            if ($array instanceof Closure) {
                $o = $array();
            } else if (isset($array->$q)) {
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
/**
* Igk getvfirst found.
* @param mixed $k
* @param array $list
* @param null|mixed $default
* @return mixed
*/
function igk_getvfirst_found($k, array $list, $default = null)
{
    if (!$list) {
        return $default;
    }
    $o = $default;
    $is_array = is_array($k);
    while (count($list) > 0) {
        $q = array_shift($list);
        if (!isset($k[$q])) {
            if ($is_array && key_exists($q, $k)) {
                $o = $k[$q];
                break;
            }
            continue;
        }
        $o = $k[$q];
        break;
    }
    return $o;
}
/**
* autoload class in dirs
* @param string $name
* @param string $entryNS
* @param string $classdir
* @param mixed & $refile
* @return mixed
*/
function igk_auto_load_class(string $name, ?string $entryNS, ?string $classdir, &$refile = null)
{
    return ApplicationLoader::getInstance()->registerLoading($name, $entryNS, $classdir, $refile);
}
if (!function_exists('igk_io_tempdir')) {
    /**
     * helper: create a tempory directory by using sys_get_temp_dir
     * @param string $prefix 
     * @return string|false the tempory directory created or false
     */
    function igk_io_tempdir(string $prefix = '')
    {
        if ($c = tempnam(sys_get_temp_dir(), $prefix)) {
            unlink($c);
            if (IO::CreateDir($c))
                return $c;
        }
        return false;
    }
}
/**
 * auto generate doc.
 * @param string $file 
 * @return string|null
 */
function igk_io_get_script(string $file)
{
    if (igk_io_file_exists($file)) {
        return "?>" . file_get_contents($file);
    }
    return null;
}
/**
 * retrieve base working dir.
 * where balafon.config.xml is founded 
 * @param $dir folder 
 * @param $config_file configuration file to check 
 * @return string
 */
function igk_io_detect_config_working_dir(string $dir, string $config_file = IGK_BALAFON_CONFIG)
{
    $r = [$dir];
    while (count($r)) {
        $q = array_shift($r);
        $f = $q . '/' . $config_file;
        if (file_exists($f)) {
            return $q;
        }
        $p = dirname($q);
        if ($q != $p)
            array_unshift($r, $p);
    }
    return $dir;
}
/**
* evalute constant and get the value
* @param mixed $name
* @param mixed $default
* @var string $name
* @return mixed|int
*/
function igk_const($name, $default = null)
{
    if (defined($name)) {
        return constant($name);
    }
    return $default;
}
/**
* check value for assertion
* @param mixed $b
* @return bool
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
/**
* check if a constant match the defvalue
* @param mixed $ctname
* @param mixed $defvalue
* @return mixed
*/
function igk_const_defined($ctname, $defvalue = 1)
{
    if (defined($ctname))
        return constant($ctname) == $defvalue;
    return false;
}
/**
* helper: check obj check for class or create a new instance by calling the callback create
* @param string $class_name name of the class to create
* @param mixed & $obj
* @param mixed $callback 
* @return mixed
*/
function igk_create_instance($class_name, &$obj, $callback)
{
    if (is_null($obj)) {
        $obj = $callback($class_name) ?? igk_die('failed to create the instance.');
    }
    return $obj;
}
/**
* get basename without extension
* @param string $file
* @return mixed
*/
function igk_io_basenamewithoutext(string $file)
{
    return igk_io_remove_ext(basename($file));
}
/**
 * filename extension
 * @param string $fname
 * @return ?string $fname
 */
function igk_io_path_ext(string $fname)
{
    if (empty($fname))
        return null;
    return ($t = explode(".", $fname)) > 1 ? array_pop($t) : "";
}
/**
* Remove extension from filename @name file name
* @param mixed $name
* @return mixed
*/
function igk_io_remove_ext($name)
{
    if (empty($name))
        return '';
    $t = explode(".", $name);
    if (count($t) > 1) {
        $s = substr($name, 0, strlen($name) - strlen($t[count($t) - 1]) - 1);
        return $s;
    }
    return $name;
}
/**
* Igk io inject uri arg.
* @param mixed $uri
* @param mixed $name
* @param null|mixed & $fragment
* @return mixed
*/
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
* @param mixed $uri
* @param ?array $query
* @param mixed & $fragment
* @return mixed
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
/**
 * get application directory
 * @return string
 */
function igk_io_applicationdir()
{
    return Path::getInstance()->getApplicationDir();
}
/**
* detect that the environment in on command line mode
* @return mixed
*/
function igk_is_cmd()
{
    if (php_sapi_name() == 'cli') {
        return true;
    }
    if (isset($_SERVER["SERVER_PROTOCOL"])) {
        return false;
    }
    return ((isset($_SERVER["argv"]) && !isset($_SERVER["SERVER_PROTOCOL"]))) || igk_environment()->get("sys://func/" . __FUNCTION__);
}
/**
* Sets cmd.
* @param mixed $v
* @return mixed
*/
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
/**
* get if framework is in atomic mode
* @return mixed
*/
function igk_is_atomic()
{
    return defined("IGK_FRAMEWORK_ATOMIC") && (IGK_FRAMEWORK_ATOMIC == 1);
}
/**
 * used to include once the library file by name
 * @param string $name library name 
 * @return int
 */
function igk_load_library(string $name): int
{
    static $inUse = null;
    if (is_null($inUse)) {
        $inUse = [];
    }
    if (isset($inUse[$name])) {
        return 1;
    }
    $lib = IGK_LIB_DIR . "/Library";
    $c = $lib . "/igk_" . $name . ".php";
    $ext = igk_io_path_ext(basename($name));
    if (empty($ext) || ($ext != ".php"))
        $ext = ".php";
    foreach ([$c, $lib . "/" . $name . $ext] as $c) {
        if (igk_io_file_exists($c, true) && !isset($inUse[$c])) {
            include_once($c);
            $inUse[$c] = 1;
            return 1;
        }
    }
    return 0;
}
/**
* Igk wl tag.
* @param mixed $tag
* @return mixed
*/
function igk_wl_tag($tag)
{
    echo "<$tag>";
    foreach (array_slice($tab = func_get_args(), 1) as $c) {
        igk_wl($c);
    }
    echo "</$tag>";
}
/**
* download zip core
* @param mixed $download
* @return mixed
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
/**
* helper: get all projects
* @return mixed
*/
function igk_sys_project_controllers()
{
    return SysUtils::GetProjectControllers();
}
/**
* write content to buffer
* @param mixed $msg
* @return mixed
*/
function igk_wl($msg)
{
    // + | ---------------------------------------------------------------
    // + | BIND TRACE do not use include for speed
    // + | ---------------------------------------------------------------
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
/**
* pre print_r helper
* @param mixed $p
* @return mixed
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
/**
* Igk dev wln.
* @return mixed
*/
function igk_dev_wln()
{
    if (igk_environment()->isDev()) {
        call_user_func_array("igk_wln", func_get_args());
    }
}
/**
* Igk dev ilog.
* @return mixed
*/
function igk_dev_ilog()
{
    if (igk_environment()->isDev()) {
        call_user_func_array("igk_ilog", func_get_args());
    }
}
/**
 * helper: in dev write line and exit 
 * @return void 
 */
function igk_dev_wln_e()
{
    if (igk_environment()->isDev()) {
        $fc = defined('IGK_RUN_TAC_COMMAND') ? 'igk_wln' : 'igk_wln_e';
        call_user_func_array($fc, func_get_args());
    }
}
/**
* Binds trace.
* @return mixed
*/
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
/**
 * write to output
 * @param string ...$msg
 * @return void
 */
function igk_wln($msg = "")
{
    // + | ---------------------------------------------
    // + | BIND TRACE IF - do not include file for speed 
    // + | igk_trace();
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
    if (!($lf = igk_environment()->get(IGK_LF_KEY))) {
        $v_iscmd = igk_is_cmd();
        $lf = $v_iscmd ? IGK_CLF : "<br />";
    }
    foreach (func_get_args() as $k) {
        $msg = $k;
        if (is_string($msg) || is_numeric($msg)) {
            echo ($msg . $lf);
        } else {
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
/**
* auto generate doc.
* @param mixed $tab
* @param mixed $lf
* @return mixed
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
        $ch = '';
        foreach ($tab as $k => $v) {
            $msg .= $ch . "{$TAB}{$k}";
            if (is_object($v)) {
                $msg .= " => Object[" . get_class($v) . "]";
            } else if (is_array($v)) {
                $msg .= ":Array";
            } else
                $msg .= " => " . $v;
            $ch = ',' . $LF;
        }
        $msg .= $LF;
    }
    igk_wl($msg . ")" . $lf);
}
/**
* write line to buffer and exit
* @param mixed $msg primary data
* @param mixed ...$extra
* @return mixed
*/
function igk_wln_e($msg = "", ...$extra)
{
    igk_environment()->set('TRACE_LEVEL', 3);
    call_user_func_array('igk_wln', func_get_args());
    igk_exit();
}
/**
* Igk tag wln.
* @param mixed $tag
* @param mixed ...$args
* @return mixed
*/
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
/**
* auto generate doc.
* @param mixed $ctrl
* @return mixed
*/
function igk_app_is_appuser($ctrl)
{
    return ($u = $ctrl->User) && $u->clLogin == $ctrl->Configs->{'app.DefaultUser'};
}
/**
* get if application is on uri demand
* @param mixed $app
* @param mixed $function
* @return mixed
*/
function igk_app_is_uri_demand($app, $function)
{
    return (igk_io_currentUri() == $app->getAppUri($function));
}
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
/**
 * get the copyright 
 * @param null|bool $long 
 * @return string 
 */
function igk_sys_copyright(?bool $long = false): string
{
    if ($long)
        return "IGKDEV &copy; 2011-" . date('Y') . " " . __("all rights reserved");
    return 'IGKDEV &copy; ' . date('Y');
}
/**
* trace utility in buffer
* @param mixed $depth
* @param mixed $sep
* @param mixed $count
* @param mixed $header
* @return string|false
*/
function igk_ob_trace($depth = 0, $sep = "", $count = -1, $header = 0)
{
    return igk_ob_get_func('igk_trace', [2 + $depth, $sep, $count, $header]);
}
/**
* auto generate doc.
* @param mixed $depth the default value is 0
* @param mixed $sep
* @param mixed $count
* @param mixed $header
* @param ?bool $cmd
* @return mixed
*/
function igk_trace($depth = 0, $sep = "", $count = -1, $header = 0, ?bool $cmd = null)
{
    $callers = debug_backtrace();
    $o = "";
    $tc = 1;
    $cmd = $cmd ?? igk_is_cmd();
    if ($cmd) {
        for ($i = $depth; $i < count($callers); $i++, $tc++) {
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
    $o .= "<table style=\"border-collapse: collapse; min-width: 400; font-family: sans-serif; margin:25px 0; box-shadow: 0 0 20px rgba(0, 0, 0, 0.15); \">" . $sep;
    if ($header) {
        $o .= "<tr style=\"background-color: \">";
        $o .= "<th>&nbsp;</th>";
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
/**
* get caller file
* @param int $depth
* @return mixed
*/
function igk_sys_get_caller_file(int $depth = 0)
{
    $callers = debug_backtrace();
    $count = $depth;
    while (count($callers) > 0) {
        $q = array_shift($callers);
        if ($count <= 0) {
            if ($file = igk_getv($q, 'file')) {
                return [$file, $q];
            }
        } else {
            $count--;
        }
    }
    return null;
}
/**
* get system directory presentation shortcut
* @param mixed $dir
* @param mixed $separator
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
/**
* get system running context
* @return mixed
*/
function igk_current_context()
{
    return igk_environment()->get(IGK_ENV_APP_CONTEXT, IGKAppContext::initializing);
}
/**
* helper: get if the system is on production mode
* @return mixed
*/
function igk_sys_env_production()
{
    return igk_environment()->isOPS();
}
/**
* utility function to get server name
* @return mixed
*/
function igk_server_name(): ?string
{
    return igk_server()->SERVER_NAME;
}
/**
* extend is callable function for igk usage
* @param mixed $tab
* @return mixed
*/
function igk_is_callable($tab)
{
    if (($tab == null) || is_numeric($tab))
        return 0;
    if (is_callable($tab))
        return true;
    if (is_array($tab) && count($tab) > 2) {
        $c = array_slice($tab, 0, 2);
        return is_callable($c);
    }
    return igk_is_callback_obj($tab);
}
/**
* determine whether to only get one controller per application
* @return mixed
*/
function igk_is_singlecore_app()
{
    return igk_sys_getconfig("force_single_controller_app", igk_const("IGK_SINGLE_CONTROLLER_APP"));
}
/**
* shortcut to IGKEvents::hook
* @param mixed $name
* @param mixed $args the default value is
* @param null|array|object|\IGK\IHookOptions $options to pass default|output|type
* @return mixed
*/
function igk_hook($name, $args = array(), $options = null)
{
    return IGKEvents::hook($name, $args, $options);
}
/**
* Igk hook clear.
* @param mixed $name
* @return mixed
*/
function igk_hook_clear($name)
{
    IGKEvents::unreg_hook($name, null, true);
}
/**
* auto generate doc.
* @param mixed $name
* @param mixed $callback
* @param mixed $priority the default value is 10
* @param mixed $injectable
* @return mixed
*/
function igk_reg_hook($name, $callback, $priority = 10, $injectable = true)
{
    IGKEvents::reg_hook($name, $callback, $priority, $injectable);
}
/**
 *  return the application in the current session
 *  @return IGKApp
 */
function igk_app()
{
    return IGKApp::getInstance();
}
/**
 * helper to get system configuration
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
/**
* shortcut to get controller application controller by ref_name
* @param string $name reference name. key or class name
* @param int|bool $throwex throw exception if not found
* @param bool $register_autoload
* @return ?\IGK\Controllers\BaseController controller found
*/
function igk_getctrl(?string $name, $throwex = 1, bool $register_autoload = false)
{
    $ctrl = igk_app()->getControllerManager()->getController($name, $throwex);
    if ($ctrl && $register_autoload) {
        $ctrl::register_autoload();
    }
    return $ctrl;
}
/**
* helper: shortcut to write log
* @param array|string $message
* @param string|null $tag
* @param mixed $traceindex tracing index
* @param mixed $dblog
* @throws IGKException
* @return void
*/
function igk_ilog($message, ?string $tag = null, $traceindex = 0, $dblog = true)
{
    if (is_array($message)) {
        $message = implode(" ", $message);
    }
    IGKLog::Append($message, $tag, $traceindex, $dblog);
}
/**
 * format ilog tag message 
 * @param mixed $message 
 * @param null|string $tag 
 * @return string 
 */
function igk_logf($message, ?string $tag = null)
{
    $tag = $tag ?? Constants::LOG_TAG;
    return sprintf('[%s] - %s', $tag, $message);
}
/**
* check for existing key on object or array
* @param mixed $data
* @param string $n
* @return bool
*/
function igk_key_exists($data, string $n): bool
{
    if (is_object($data)) {
        if ($data instanceof IArrayKeyExists) {
            return $data->keyExists($n);
        }
        return property_exists($data, $n);
    } else if (is_array($data)) {
        return key_exists($n, $data);
    }
    return false;
}
if (!function_exists('igk_ilog_m')) {
    /**
     * get ilog message string
     * @param mixed $msg 
     * @param string $tag 
     * @return string 
     */
    function igk_ilog_m($msg, $tag = IGK_LOG_SYS): string
    {
        return sprintf('[%s] - %s', $tag, $msg);
    }
}
// + | IO shortcut
/**
* shortcut to IO::GetBaseUri
* @param mixed $dir null or existing fullpath directory or file element.
* @param mixed $secured
* @param mixed & $path
* @return mixed
*/
function igk_io_baseuri($dir = null, $secured = null, &$path = null)
{
    return Path::getInstance()->baseuri($dir, $secured, $path);
}
/**
* return the current page folder
* @return mixed
*/
function igk_io_current_page_folder()
{
    return igk_app()->getCurrentPageFolder();
}
/**
 * get relative path to rootdir if exists
 * @param string $dir cwd path or full path
 * @param string $sep separator
 * @return string return path
 */
function igk_io_basepath(string $dir, string $sep = DIRECTORY_SEPARATOR)
{
    return Path::getInstance()->basepath($dir, $sep);
}
/**
* get path from base directory
* @param ?string $dir
* @return string
*/
function igk_io_basedir(?string $dir = null): string
{
    return Path::getInstance()->basedir($dir);
}
/**
* retrieve the data folder shortuct
* @return mixed
*/
function igk_io_sys_datadir(): string
{
    return Path::getInstance()->getSysDataDir();
}
/**
* get system configuration value
* @param mixed $name
* @param mixed $defaultvalue
* @return mixed
*/
function igk_sys_getconfig($name, $defaultvalue = null)
{
    return igk_getv(IGKAppConfig::getInstance()->Data, $name, $defaultvalue);
}
/**
 * save file helper
 * @param mixed $file
 * @param mixed $content
 * @param mixed $overwrite the default value is true
 * @param mixed $chmod the default value is IGK_DEFAULT_FILE_MASK
 * @param mixed $type the default value is "w+"
 * @return bool 
 */
function igk_io_w2file($file, $content, $overwrite = true, $chmod = IGK_DEFAULT_FILE_MASK, $type = "w+")
{
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
if (!function_exists('igk_conf_get')) {
    /**
    * get object with igk XPath selection model
    * @param mixed $conf object to get
    * @param string $path the path configuration
    * @param mixed $default default value in case of not found
    * @param mixed $strict
    * @return null|mixed default|mixed|null or founded object
    */
    function igk_conf_get($conf, string $path, $default = null, $strict = 0)
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
                                if ($m == null) {
                                    $m = $s;
                                } else {
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
                        if ($p) {
                            $m = $q;
                        } else {
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
                } else {
                    if (!is_array($tout)) {
                        $tout = array($tout);
                    }
                    $tout[] = $q;
                }
            }
        }
        return $tout;
    }
}
/**
* get the current subdomain from uri
* @param mixed $uri
* @return mixed
*/
function igk_io_subdomain_uri_name($uri = null)
{
    return IGKSubDomainManager::SubDomainUriName($uri);
}
/**
* get current domain from uri
* @param mixed $uri
* @return mixed
*/
function igk_io_domain_uri_name($uri = null)
{
    return IGKSubDomainManager::DomainUriName($uri);
}
/**
* return the full request uri
* @return mixed
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
/**
 * load library specification
 * @param string $dir directory to load
 * @param string $ext extension file or regext to used for matching class
 * @param array<string> $excludedir directory to exclude
 * @return null|array $files loaded
 * */
function igk_loadlib(string $dir, string $ext = ".php", ?array $excludedir = null): ?array
{
    igk_debug_wln('[' . __FUNCTION__ . '] - ' . $dir);
    $sdir = is_dir($dir) ? $dir : igk_dir(igk_realpath($dir));
    if (empty($sdir)) {
        return null;
    }
    $v_env = igk_environment();
    $dir = $sdir;
    $excluded_key = IGKEnvironment::IGNORE_LIB_DIR;
    $excludedir = $excludedir ?? array_merge(igk_get_env($excluded_key) ?? [], igk_default_ignore_lib());
    if (!$excludedir)
        $excludedir = array();
    $m = &$excludedir;
    $v_env->set($excluded_key,  $m);
    return igk_loadlib_dirs($dir, $ext, $excludedir);
}
/**
* auto generate doc.
* @param string $dir
* @param string $ext
* @param mixed & $excludedir
* @param mixed $project
* @var string $dirs list of root directory
* @return mixed
*/
function igk_loadlib_dirs(string $dir, string $ext = ".php", &$excludedir = null, $project = true)
{
    $files = [];
    $loadeds = [];
    $root = true;
    $ln = null;
    $extensions = explode("|", $ext);
    $dirs = [$dir];
    while (igk_count($dirs) > 0) {
        $dir = array_shift($dirs);
        if (is_null($ln)) {
            $dir = realpath($dir);
            $ln = strlen($dir);
        }
        if (isset($excludedir[$dir]))
            continue;
        $hdir = @opendir($dir);
        if (!$hdir)
            continue;
        $file = IGK_STR_EMPTY;
        if (!$root && $project && is_file($gdir = $dir . "/.global.php")) {
            include_once($gdir);
            $files[] = igk_uri($gdir);
            $loadeds[$gdir] = 1;
            if (isset($excludedir[$dir])) {
                closedir($hdir);
                continue;
            }
        }
        while ($fdir = readdir($hdir)) {
            if (($fdir == ".") || ($fdir == "..") || isset($excludedir[$fdir]))
                continue;
            $file = $dir . DIRECTORY_SEPARATOR . $fdir;
            if (is_dir($file)) {
                // + | exclude named directery
                if (isset($excludedir[$file]) || ($fdir[0] == ".") || isset($excludedir[$fdir])) {
                    $excludedir[$file] = 1;
                    continue;
                }
                $dirs[] = $file;
            } else {
                if (isset($loadeds[$file]) || preg_match("/\[.+\]/", igk_io_basenamewithoutext($fdir)))
                    continue;
                $t_ext = igk_io_path_ext($file);
                if (!in_array('.' . $t_ext, $extensions))
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
        $root = false;
    }
    return $files;
}
// + | request helper function 
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
    /**
    * get request value
    * @param mixed $key
    * @param mixed $value
    * @return mixed
    */
    function igk_getr($key, $value = null)
    {
        return igk_get_tab_value($_REQUEST, $key, $value);
    }
}
if (!function_exists('igk_getr_post')) {
    /**
     * retrive post only parameter
     * @param mixed $key 
     * @param mixed $value 
     * @return mixed 
     */
    function igk_getr_post($key, $value = null)
    {
        return igk_get_tab_value($_POST, $key, $value);
    }
}
/**
* get GET value
* @param mixed $key
* @param mixed $value
* @return mixed
*/
function igk_getg($key, $value = null)
{
    return igk_get_tab_value($_GET, $key, $value);
}
/**
* get a check POST value
* @param mixed $key
* @param mixed $value
* @return mixed
*/
function igk_getp($key, $value = null)
{
    return igk_get_tab_value($_POST, $key, $value);
}
/**
* get session param value
* @param mixed $key
* @param mixed $value
* @return mixed
*/
function igk_gets($key, $value = null)
{
    return igk_get_tab_value($_SESSION, $key, $value);
}
/**
* auto generate doc.
* @param mixed $key
* @param mixed $value
* @return mixed
*/
function igk_getru($key, $value = null)
{
    if (is_object($key))
        return $value;
    if (isset($_REQUEST[$key]))
        return str_replace("-", "_", igk_str_quotes($_REQUEST[$key]));
    return $value;
}
/**
* get the value between value and default. if $value is empty or null default
* @param mixed $value
* @param mixed $default
* @return mixed
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
* @return mixed
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
/**
* shortcut helper
* @param mixed $dirname
* @param mixed $mode the default value is IGK_DEFAULT_FOLDER_MASK
* @return mixed
*/
function igk_io_createdir($dirname, $mode = IGK_DEFAULT_FOLDER_MASK)
{
    require_once IGK_LIB_CLASSES_DIR . '/Helper/IO.php';
    igk_wln_e("try create a directory : ", $dirname, class_exists('IGK\\Helper\\IO'));
    return IO::CreateDir($dirname, $mode);
}
/**
* count helper
* @param mixed $item
* @return mixed
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
        if (method_exists(get_class($item), 'getCount'))
            return $item->getCount();
        if (method_exists(get_class($item), 'getRowCount'))
            return $item->getRowCount();
    }
    return 0;
}
/**
* return the base request uri - start at $basedir
* @param mixed $rm_redirectvar
* @return mixed
*/
function igk_io_base_request_uri($rm_redirectvar = 1)
{
    $s = igk_io_baseuri();
    $d = igk_io_fullrequesturi();
    $o = '/' . ltrim(substr($d, strlen($s)), '/');
    ($rm_redirectvar) && igk_io_rm_redirectvar($o);
    return $o;
}
/**
* remove redirected query var form query
* @param mixed & $uri
* @param mixed $force
* @return mixed
*/
function igk_io_rm_redirectvar(&$uri, $force = 0)
{
    if ($force || igk_server()->REDIRECT_STATUS == 200) {
        if ($g = parse_url($uri)) {
            $tab = array();
            if (isset($g["query"])) {
                parse_str($g["query"], $tab);
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
}
/**
 * create and fill stdClass from array or object
 * @param mixed $tab 
 * @return stdClass 
 */
function igk_createobj($tab = null): stdClass
{
    $o = new stdClass();
    if ($tab) {
        foreach ($tab as $k => $v) {
            $o->$k = $v;
        }
    }
    return $o;
}
/**
* auto generate doc.
* @param object $n
* @return mixed
*/
function igk_is_class_incomplete($n)
{
    return get_class($n) === __PHP_Incomplete_Class::class;
}
/**
 * get realpath helper
 * @param mixed $p 
 * @return string|false|null 
 * @throws IGKException 
 */
function igk_realpath(string $p)
{
    return Path::getInstance()->realpath($p);
}
/**
* check if is sub directory
* @param mixed $p
* @param mixed $c
* @return mixed
*/
function igk_io_is_subdir($p, $c)
{
    return IO::IsSubDir($p, $c);
}
/**
* return default configuration settings
* @return mixed
*/
function igk_sys_getdefaultctrlconf()
{
    return array(
        "clVersion" => "1.0",
        "clDataAdapterName" => igk_configs()->get("default_dataadapter", IGK_CSV_DATAADAPTER),
        IGK_CTRL_CNF_USE_DATASCHEMA => false,
        "clDisplayName" => null,
        "clRegisterName" => null,
        "clParentCtrl" => null,
        "clTargetNodeIndex" => 0,
        "clVisiblePages" => "*",
        "clDescription" => null,
    );
}
/**
* get cached reflected class
* @param mixed $cl
* @param mixed & $reference
* @return ?ReflectionClass
*/
function igk_sys_reflect_class($cl, &$reference = null)
{
    static $reflection;
    if (is_null($reflection)) {
        $reflection = [];
    }
    $reference = $reflection;
    if (is_null($cl)) {
        return null;
    }
    if (is_object($cl)) {
        $cl = get_class($cl);
    }
    if (isset($reflection[$cl])) {
        return $reflection[$cl];
    }
    if (is_string($cl) && (class_exists($cl) || trait_exists($cl) || interface_exists($cl))) {
        $rf = new ReflectionClass($cl);
        $reflection[$cl] = $rf;
        return $rf;
    }
    igk_trace();
    igk_dev_wln_e(__FILE__ . ":" . __LINE__, "core: missing class ::: " . $cl);
}
/**
* Igk sys reflect class unset.
* @param mixed $cl
* @return mixed
*/
function igk_sys_reflect_class_unset($cl)
{
    igk_sys_reflect_class(null, $reference);
    unset($reference[$cl->getName()]);
}
if (!function_exists('igk_sys_reflect_get_constants')) {
    /**
     * reflect class get constants
     * @param mixed $cl 
     * @return array 
     * @throws Exception 
     * @throws IGKException 
     */
    function igk_sys_reflect_class_get_constants($cl)
    {
        $ref = igk_sys_reflect_class($cl);
        return $ref->GetConstants();
    }
}
/**
 * helper: check if support trait
 * @param mixed $object_or_class 
 * @param mixed $trait 
 * @return bool 
 */
function igk_sys_support_trait($object_or_class, $trait)
{
    return TraitHelper::SupportTrait($object_or_class, $trait);
}
if (!function_exists('igk_io_workingdir')) {
    /**
    * get working directory
    * @param bool $server
    * @throws \IGKException
    * @return string|false
    */
    function igk_io_workingdir(bool $server = true)
    {
        $v_key = 'IGK_WORKING_DIR';
        if (defined($v_key)) {
            return constant($v_key);
        }
        if ($server && isset($_SERVER[$v_key])) {
            return $_SERVER[$v_key];
        }
        $app_dir = igk_io_applicationdir();
        $base_dir = igk_io_basedir();
        if ($app_dir == $base_dir) {
            define($v_key, $app_dir);
            return $app_dir;
        }
        $c = 0;
        while ($app_dir && ($app_dir != "/")) {
            $app_dir = dirname($app_dir);
            $c++;
            if ($c > 10) break;
            if (strstr($base_dir, $app_dir)) {
                define($v_key, $app_dir);
                return $app_dir;
            }
        }
        if (IGKApp::IsInit()) {
            define($v_key, $dir = getcwd());
            return $dir;
        }
        igk_die("failed to found working directory " . getcwd());
    }
}
/**
* auto generate doc.
* @param mixed $prefix
* @return mixed
*/
function igk_io_tempfile($prefix = 'tmp')
{
    return tempnam(sys_get_temp_dir(), $prefix);
}
/**
 * application environment setting
 * @return \IGK\EnvironmentSettings environment setting
 */
function igk_setting()
{
    require_once IGK_LIB_CLASSES_DIR . "/EnvironmentSettings.php";
    return \IGK\EnvironmentSettings::getInstance();
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
/**
* return an array of default sys ignored folder keys
* @param mixed $dir
* @return mixed
*/
function igk_default_ignore_lib($dir = null)
{
    $tk =
        [
            IGK_LIB_FOLDER => 1,
            IGK_CONF_FOLDER => 1,
            IGK_DATA_FOLDER => 1,
            IGK_VIEW_FOLDER => 1,
            IGK_CONTENT_FOLDER => 1,
            IGK_SCRIPT_FOLDER => 1,
            IGK_STYLE_FOLDER => 1,
            IGK_ARTICLES_FOLDER => 1,
            IGK_CGI_BIN_FOLDER => 1,
            IGK_TESTS_FOLDER => 1,
            IGK_GIT_FOLDER => 1,
            IGK_NODE_MODULE_FOLDER => 1,
            '.vscode' => 1,
            'command-scripts' => 1,
            'command-bin' => 1
        ];
    if ($dir) {
        $keys = array_keys($tk);
        foreach ($keys as $m) {
            $tk[igk_uri($dir . '/' . $m)] = 1;
        }
    }
    return $tk;
}
if (!function_exists('igk_uri')) {
    /**
    * helper: shorcut string as uri path
    * @param string $u path to convert
    * @return mixed
    */
    function igk_uri(string $u): string
    {
        return stringUtility::Uri($u);
    }
}
if (!function_exists('igk_uri_path')) {
    /**
     * get base path helper
     * @param string $url 
     * @return void 
     */
    function igk_uri_path(string $url)
    {
        $q = parse_url($url);
        return igk_getv($q, 'path');
    }
}
if (!function_exists('igk_uri_base_path')) {
    /**
     * get base path helper
     * @param string $url 
     * @return void 
     */
    function igk_uri_base_path(string $url)
    {
        $u = igk_io_baseuri();
        if (str_starts_with($url, $u)) {
            $url = substr($url, strlen($u));
        }
        $q = parse_url($url);
        return igk_getv($q, 'path');
    }
}
if (!function_exists('igk_uri_base_uri')) {
    /**
    * retrieve base uri
    * @param string $url
    * @return string
    */
    function igk_uri_base_uri(string $url): string
    {
        $tp = parse_url($url);
        $host = igk_getv($tp, 'host') ?? igk_die('missing host');
        $p = [];
        $p[] = igk_getv($tp, 'scheme') ?? 'https';
        $p[] = '://';
        $p[] = $host;
        if ($port = igk_getv($tp, 'port')) {
            $p[] = ":" . $port;
        }
        return implode('', $p);
    }
}
/**
* check if $c is a framework callback object
* @param mixed $c the callback object to check
* @return mixed
*/
function igk_is_callback_obj($c)
{
    $s = IGK_OBJ_TYPE_FD;
    return (is_array($c) && isset($c[$s]) && ($c[$s] == "_callback")) || (is_object($c) && !is_callable($c) && isset($c->$s) && ($c->$s == "_callback"));
}
/**
* call it to ignore a specific directory on javascript loading process.
* @param mixed $dir if dir is null or not an existing directory, return the current directory list- use configuration files to ignore directory for loading process
* @return mixed
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
/**
* register global balafon settings
* @param mixed $n
* @param mixed $d
* @param mixed $desc
* @return mixed
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
    return 1;
}
/**
 * load environment files
 * @param string $dirname
 * @param array $tab list of folder to load. if relative to dirname or absolute paht
 * @return array loaded files
 */
function igk_load_env_files(string $dirname, $tab = [IGK_INC_FOLDER, IGK_PROJECTS_FOLDER])
{
    $t_files = array();
    igk_hook("sys://event/cachelibreload", array(null, (object)array("files" => &$t_files)));
    $tab = $tab == null ? array(IGK_INC_FOLDER, IGK_PROJECTS_FOLDER) : $tab;
    $bckdir = getcwd();
    chdir($dirname);
    while (count($tab) > 0) {
        $s = array_shift($tab);
        if (empty($s))
            continue;
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
* @param ?string $version
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
/**
* auto generate doc.
* @param int $code
* @param mixed $message
* @param mixed $headers
* @return mixed
*/
function igk_set_header(int $code, $message = "", $headers = [])
{
    if (igk_is_cmd() || headers_sent())
        return false;
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
    igk_clear_header_list();
    $msg = igk_get_header_status($code);
    $txt = "Status: {$code} $msg";
    if (!$fcall) {
        if ($new) {
            header($txt);
            header(IGK_FRAMEWORK . ":" . IGK_CODE_NAME . "-" . IGK_VERSION);
            // + | -----------------------------------------------------------------
            // + | for new security strict on https request demand 
            // + |  
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
            header('X-Content-Type-Options: nosniff');
            $sess_id = session_id();
            if ($sess_id) {
                $sess_name = session_name();
                $domain = igk_get_cookie_domain();
                // + | --------------------------------------------------------------------
                // + | attach new cookies - session if no present
                // + |                
                if (!preg_match('/\\b' . $sess_name . '\\b/', igk_getv($h, 'COOKIE', '')) || (igk_getv($_COOKIE, $sess_name) !== $sess_id)) {
                    $r = 'Set-Cookie: ' . igk_sys_cookies_build([$sess_name => $sess_id . '; path=/; HttpOnly; domain=' . $domain . ';']);
                    if (igk_server()->is_secure()) {
                        $r .= 'Secure;';
                    }
                    header($r);
                }
            }
        }
    } else {
        header($txt, 1, $code);
    }
    igk_environment()->isDev() && header("srv-msg:" . $message);
    if ($headers) {
        // + | replace with setup header 
        foreach ($headers as $m => $k) {
            header($k, !is_numeric($m));
        }
    }
    // +| the last one set the code status
    header($msg, 1, $code);
    $fcall = 1;
}
/**
* bind my how header
* @return mixed
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
/**
* Represent igk_get_header_status function
* @param mixed $code
* @return mixed
*/
function igk_get_header_status($code)
{
    return \IGK\System\Http\StatusCode::GetStatus($code);
}
/**
* specifu cache output
* @param mixed $second
* @return mixed
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
* @param mixed & $sdata
* @param mixed $tdata
* @return mixed
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
if (!function_exists('igk_bool')) {
    /**
     * parse to bool 
     * @param mixed $data 
     * @return bool 
     */
    function igk_bool($data)
    {
        if (is_bool($data)) {
            return $data;
        }
        $v = $data;
        if ($v && (is_string($v) && in_array(strtolower($v), ['true', 'false', '1', '0']))) {
            $v = (bool)preg_match("/(true|1)/i", $v);
        }
        return (bool)boolval($v);
    }
}
/**
* dump array
* @param mixed ...$args
* @return void
*/
function igk_dump_array(...$args)
{
    igk_wl(...$args);
}
if (!function_exists('igk_sys_reflect_public_class_var')) {
    /**
    * auto generate doc.
    * @param string $class_name
    * @return mixed
    */
    function igk_sys_reflect_public_class_var(string $class_name)
    {
        if ($v_r = igk_sys_reflect_class($class_name)) {
            $v_filter_p = [];
            foreach ($v_r->getProperties(ReflectionProperty::IS_PUBLIC) as $p) {
                if (!$p->isStatic()) $v_filter_p[] = $p->name;
            }
            return $v_filter_p;
        }
    }
}
if (!function_exists('igk_get_object_public_vars')) {
    /**
     * encapsulate get_object_vars to make it call outside the class context 
     * @param mixed $obj 
     * @return array 
     */
    function igk_get_object_public_vars($obj)
    {
        return get_object_vars($obj);
    }
}
if (!function_exists('igk_sys_detect_project_controller')) {
    /**
    * Igk sys detect project controller.
    * @param string $project_dir
    * @return mixed
    */
    function igk_sys_detect_project_controller(string $project_dir)
    {
        $dir = $project_dir;
        $s = [];
        if ($c = opendir($dir)) {
            while ($l = readdir($c)) {
                if (preg_match("/\.php$/", $l)) {
                    include_once($dir . "/" . $l);
                    $n = igk_io_basenamewithoutext($l);
                    if (class_exists($n, false) && is_subclass_of($n, BaseController::class)) {
                        $s[] = $n;
                    }
                }
            }
            closedir($c);
        }
        $project = array_shift($s);
        return $project;
    }
}
if (!function_exists('igk_clamp')) {
    /**
     * clamp value
     * @param mixed $n 
     * @param mixed $max 
     * @param int $min 
     * @return mixed 
     */
    function igk_clamp($n, $max, $min = 0)
    {
        return max(min($max, $n), $min);
    }
}
if (!function_exists('igk_current_ctrl')) {
    /**
    * get environment core controller/project
    * @return mixed
    */
    function igk_current_ctrl()
    {
        return igk_environment()->get(IGKEnvironment::CURRENT_CTRL);
    }
}
if (!function_exists('igk_sys_lib_filename')) {
    /**
     * in production just return a collapsed file name
     * @param string $file 
     * @return string 
     * @throws IGKException 
     */
    function igk_sys_lib_filename(string $file): string
    {
        if (igk_environment()->isOPS()) {
            return igk_io_collapse_path($file);
        }
        return $file;
    }
}
if (!function_exists('igk_controller_from_dir')) {
    /**
    * retrieve the first controller that binded in declared directory
    * @param string $dir
    * @return mixed
    */
    function igk_controller_from_dir(string $dir)
    {
        $c = igk_sys_get_projects_controllers();
        foreach ($c as $ctrl) {
            if ($ctrl->getDeclaredDir() == $dir) {
                return $ctrl;
            }
        }
        return null;
    }
}
if (!function_exists('igk_read_line')) {
    /**
     * write read line
     * @param string $prompt 
     * @return string|false 
     */
    function igk_read_line(string $prompt)
    {
        if (version_compare(PHP_VERSION, '8.0', '>=')) {
            return readline($prompt);
        }
        fwrite(STDERR, $prompt);
        return readline();
    }
}
/**
 * just find a user
 * @param string $name 
 * @return \IGK\Models\Users|mixed 
 */
function igk_sys_find_auth_user(string $name)
{
    if ($user = igk_get_user_bylogin($name)) {
        return $user;
    }
    $r = igk_hook(IGKEvents::HOOK_FIND_USER, ['name' => $name]);
    return $r;
}
if (!function_exists('igk_prop_exists')) {
    /**
     * get property exists key 
     * @param mixed $obj
     * @param mixed $property string is pipe separated string or array<string>
     * @return array
     */
    function igk_prop_exists($obj, $property): array
    {
        if (is_string($property)) {
            $property = explode('|', $property);
        }
        $r = [];
        $fc_check = function ($obj, $v) use (&$r) {
            return isset($obj[$v]);
        };
        if (is_object($obj)) {
            $fc_check = function ($obj, $v) {
                return property_exists($obj, $v);
            };
        }
        foreach ($property as $v) {
            $r[$v] = $fc_check($obj, $v);
        }
        return array_values($r);
    }
}

/**
 * copy and return a reference . 
 * - used with extract to diseable error that said need to pass a reference to extract method
 * @param mixed $tab 
 * @return mixed 
 */
function &igk_extract_ref($tab)
{
    return $tab;
}
if (function_exists('igk_curl_close')) {
    /**
    * auto generate doc.
    * @param mixed $r
    * @return void
    */
    function igk_curl_close($r)
    {
        if (version_compare(PHP_VERSION, "8.0", "<") && function_exists('curl_close'))
            curl_close($r);
    }
}
