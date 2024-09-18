<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKEnvironment.php
// @date: 20220803 13:48:54
// @desc: 



use IGK\Controllers\BaseController;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\Resources\R;
use IGK\System\Console\ServerFakerInput;
use IGK\System\IO\FakeInput;
use IGK\System\Providers\ClassProvider;
use Spatie\PhpUnitWatcher\Screens\Phpunit;

use function igk_getv as getv;


///<summary>use to manage Server Environment</summary>
/**
 * use to manage Server Environment configuration poperties
 * @property string $subdomainctrl current subdomain controller
 * @property string $basectrl base controller
 * @property string $cookie_name base controller
 * @property string $workingDir start working dir
 * @property int    $querydebug activate of not the query debug
 * @property array  $db_adapter get registered data adapters
 * @property bool   $no_lib_cache no library cache
 * @property null|array $extra_config extra configuration file
 * @property bool   $handle_ctrl_request flag that handle controller request . subdomain usage
 * @property bool   $isAJXDemand flag that handle controller request . subdomain usage
 * @property bool   $no_handle_error flag that allow environment to handle exception
 * @property bool   $NO_DB_LOG flag disable log in database
 * @property bool   $NO_PROJECT_AUTOLOAD flag disable file autoload. on project register
 * @property ?\IGK\System\IO\FakeInput $FakerInput faker input in render
 * @property ?IGKActionBase $action_handler_instance 
 * @property bool $NoLogEval disable eval log - 
 * @property bool $NoConsoleLogger disable console logger - 
 */
final class IGKEnvironment extends IGKEnvironmentConstants
{
    private static $sm_instance;
    private static $sm_states = [];
    public static function saveState(array $environment_new_state)
    {
        $bck = [];
        $env = self::getInstance();
        foreach ($environment_new_state as $k => $v) {
            $old = $env->get($k);
            $env->set($k, $v);
            $bck[$k] = $old;
        }
        self::$sm_states[] = $bck;
    }
    public static function restoreState()
    {
        $env = self::getInstance();
        if ($bck = array_pop(self::$sm_states)) {
            foreach ($bck as $k => $v) {
                $env->set($k, $v);
            }
        }
    }
    public function getAuthor()
    {
        return igk_configs()->get('author', IGK_AUTHOR);
    }
    public function getsession_cookie_name()
    {
        return defined('IGK_APP_SESS_COOKIE_NAME') ?
            defined('IGK_APP_SESS_COOKIE_NAME') : 'PHPSESSID';
    }
    /**
     * check if environment support webconfiguration
     */
    public function  noWebConfiguration(): ?bool
    {
        return defined('IGK_NO_WEBCONFIG') || igk_configs()->get("noWebConfiguration");
    }
    /**
     * key name
     * @var ?string
     */
    private $m_keyname;
    /**
     * environment properties
     * @var array
     */
    private $m_envs;
    // | default FOUR ENVIRONMENT TYPE
    private static $env_keys = [
        "DEV" => "development",
        "TST" => "testing",
        "ACC" => "acceptance",
        "OPS" => "production"
    ];

    public function getEnvironments()
    {
        return $this->m_envs;
    }
    public function getLocale()
    {
        return R::GetLocale();
    }
    /**
     * get true if environment is unix type
     */
    public function isUnix()
    {
        return in_array(strtolower(PHP_OS), ["unix", "linux", "darwin"]);
    }

    public function getPhpCoreVersion()
    {
        static $version;
        if ($version == null) {
            list($major, $minor) = explode(".",  PHP_VERSION);
            $version = $major . "." . $minor;
        }
        return $version;
    }

    public function write_debug(string $message)
    {
        $d = &$this->createArray("debug_load");
        $d[] = "<span>" . (count($d) + 1) . "</span> " . $message;
    }
    /**
     * get peek environment context 
     * @param mixed $n 
     * @return mixed 
     */
    public function peek($n)
    {
        $tab = $this->get($n);
        if (is_array($tab) && (($c = igk_count($tab)) > 0)) {
            $r = $tab[$c - 1];
            return $r;
        }
        return null;
    }
    /**
     * init this environment with a callable
     */
    public function init($key, callable $callback)
    {
        $c = $this->get($key);
        if ($c == null) {
            $c = $callback();
            $this->set($key, $c);
        }
        return $c;
    }
    /**
     * get the environment base directory
     * @return mixed 
     */
    public function getBaseDir()
    {
        return getv($this->m_envs, "basedir");
    }
    /**
     * get environment basedirectory
     */
    public function setBaseDir($basedir)
    {
        $this->set("basedir", $basedir);
        return $this;
    }

    public function getEnvironmentPath()
    {
        // 
        // resolv path to system location 
        // 
        $app_dir = igk_io_applicationdir();
        $mod_dir = igk_get_module_dir();
        $packagedir = igk_io_packagesdir();
        $lib = realpath($lib = $app_dir . "/Lib/igk") == IGK_LIB_DIR ? $lib : IGK_LIB_DIR;
        $mod_dir = realpath($rs = $packagedir . "/Modules") ==  $mod_dir ? $rs : $mod_dir;
        return array_filter([
            "%lib%" => $lib,
            "%app%" => $app_dir,
            "%project%" => igk_io_projectdir(),
            "%basedir%" => igk_io_basedir(),
            "%packages%" => $packagedir,
            "%modules%" => $mod_dir,
            "%nodepackages%" => $packagedir . "/node_modules",
            "%viewcaches%" => igk_is_cmd() ? null : $this->getViewCacheDir()
        ]);
    }
    /**
     * return view cache directory. 
     * @return string cache directory 
     */
    public function getViewCacheDir()
    {
        return IGKCaches::view()->path;
    }

    /*
    * get the environment base directory
    * @return mixed 
    */
    public function getLogFile()
    {
        return getv($this->m_envs, "logfile");
    }
    /**
     * get environment basedirectory
     */
    public function setLogFile($logfile)
    {
        $this->set("logfile", $logfile);
        return $this;
    }

    /**
     * override base uri detection by set the environment base uri
     * @param null|string $uri 
     * @return void 
     */
    public function setBaseURI(?string $uri)
    {
        $this->set("baseURI", $uri);
    }

    /**
     * create an environment class instance
     * @param mixed $classname class name declaration
     * @return mixed 
     * @throws Exception 
     */
    public function createClassInstance($classname, $callback = null)
    {
        $b = $this->instances;
        if ($b === null) {
            $b = [];
            $this->instances = $b;
        }

        if (!isset($b[$classname])) {
            $o = null;
            if ($callback) {
                if (method_exists($callback, "bindTo"))
                    $callback = $callback->bindTo(null, $classname);
                $o = $callback();
            } else {
                $o = new $classname();
            }
            $b[$classname] = $o;
            return $o;
        }
        if (!isset($b[$classname])) {
            $b[$classname] = new $classname();
        }
        return getv($b, $classname);
    }
    /**
     * return resolved enviromnent key
     * @param mixed $n 
     * @return int|string|false 
     */
    public static function ResolvEnvironment($n)
    {
        if (($index = array_search(strtolower($n), self::$env_keys)) === false) {
            return "DEV";
        }
        return $index;
    }


    public function setArray($name, $key, $value)
    {
        $tab = $this->get($name);
        if (!is_array($tab)) {
            if ($tab !== null) die("property name already contains a non array value");
            $tab = array();
        }
        $tab[$key] = $value;
        $this->set($name,  $tab);
        return $this;
    }
    public function getArray($name, $key, $default = null)
    {
        $b = $this->get($name);
        if (is_array($b)) {
            return igk_getv($b, $key, $default);
        }
        return $default;
    }
    ///<summary></summary>
    /**
     * 
     */
    private function __construct()
    {
        $this->_prepareServerEnvironment();
    }
    /**
     * prepare server environment
     * @return void 
     */
    private function _prepareServerEnvironment()
    {
        $t = [];
        foreach ($_SERVER as $k => $v) {
            if (strpos($k, "IGK_") === 0) {
                $t[$k] = $v;
            }
        }
        $this->m_envs = $t;
    }
    public function getToday()
    {
        return date("Y-m-d");
    }
    public function __debugInfo()
    {
        return null;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
     * 
     * @param mixed $n
     */
    public function &__get($n)
    {
        return $this->get($n);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
     * 
     * @param mixed $n
     */
    public function __isset($v)
    {
        return array_key_exists($v, $this->m_envs);
    }
    public function setNo_cache($value)
    {
        $this->m_envs["no_cache"] = $value;
        return $this;
    }
    public function getNo_cache()
    {
        return igk_getv($this->m_envs, "no_cache");
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $n
     * @param mixed $v
     */
    public function __set($n, $v)
    {

        if (method_exists($this, $fc = "set" . $n)) {
            $this->$fc($v);
        } else {
            $this->set($n, $v);
        }
        return $this;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function __sleep()
    {
        igk_die("Sleep Environment: Operation Not allowed " . __CLASS__);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function __wakeup() {}
    ///<summary></summary>
    ///<param name="var"></param>
    /**
     * 
     * @param mixed $var
     */
    public function &get($var, $default = null)
    {
        $t = null;
        if (empty($var)) {
            return $default;
        }
        if (method_exists($this, $fc = "get" . $var)) {
            $t = $this->$fc();
        } else {
            if (array_key_exists($var, $this->m_envs)) {
                $t = &$this->m_envs[$var];
            }
        }
        if ($t === null) {
            if (($default !== null) && igk_is_callable($default)) {
                if (($m = call_user_func_array($default, array())) !== null) {
                    $this->m_envs[$var] = $m;
                    return $m;
                }
            }
            $t = $default;
        }
        return $t;
    }
    ///<summary>create a environment class </summary>
    /**
     * create an instance of classes
     * @param string $classname_or_provider_name provider name  
     * @return mixed|null
     * @throws IGKException 
     */
    public static function GetClassInstance(string $classname)
    {
        static $instance;
        if ($instance === null)
            $instance = [];
        if (isset($instance[$classname])) {
            return $instance[$classname];
        }
        $cl = ClassProvider::GetClass($classname) ?? (class_exists($classname) ? $classname : null);
        if ($cl) {
            $c = new $cl();
            $instance[$classname] = $c;
            if ($cl != $classname) {
                $instance[$cl] = $c;
            }
            igk_environment()->set(self::INSTANCES, $instance);
            return $c;
        }
        return null;
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
     * 
     * @return self environment instance
     */
    public static function getInstance()
    {
        if (is_null(self::$sm_instance)) {
            self::$sm_instance = new self();
        }
        return self::$sm_instance;
    }

    public function is_mod_enabled($module)
    {
        return igk_apache_module($module);
    }

    public function &require_modules()
    {
        $k = IGKEnvironmentConstants::REQUIRE_MODULES;
        $v_k = &$this->get($k);
        if (!$v_k) {
            $v_k = [];
            $this->m_envs[$k] = &$v_k;
        }
        return $v_k;
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getVars()
    {
        return $this->m_envs;
    }
    ///<summary>check wether environment is on environment mode</summary>
    ///<remark>default environment mode is *development</summary>
    /**
     * check wether environment is on environment mode
     */
    public function is($env_mode)
    {
        if (array_key_exists($env_mode, self::$env_keys)) {
            $env_mode = self::$env_keys[$env_mode];
        }
        return igk_server()->ENVIRONMENT == $env_mode;
    }
    /**
     * helper
     * @return bool 
     */
    public function isDev(): bool
    {
        return $this->is(self::DEV_ENV);
    }
    public function isOPS(): bool
    {
        return $this->is(self::OPS_ENV);
    }
    ///<summary>get if environment is in debug mode</summary>
    /**
     * get if environment is in debug mode
     * @return boolean
     */
    public function isDebug(){
        return defined('IGK_DEBUG') ? constant('IGK_DEBUG') : igk_environment()->get(self::DEBUG);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function IsWebApp()
    {
        return $this->get("IGK_APP") == "WEBAPP";
    }
    /**
     * get environment application context
     * @return ?string 
     */
    public function context()
    {
        return $this->get("app_type", IGKAppType::web);
    }
    ///<summary>environment full name</summary>
    /**
     * environment full name
     */
    public function name()
    {
        return igk_server()->ENVIRONMENT;
    }
    ///<summary>environment short name</summary>
    /**
     * environment - started short name
     */
    public function keyName()
    {
        if (is_null($this->m_keyname)) {
            $this->m_keyname = self::ResolvEnvironment($this->name());
        }
        return $this->m_keyname;
    }
    /**
     * alway resolve the key name depending on environment state
     * @return int|string|false 
     */
    public function resolvKeyName()
    {
        return self::ResolvEnvironment($this->name());
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /** 
     * @param mixed $i
     */
    protected function _access_offsetExists($i): bool
    {
        return isset($this->m_envs[$i]);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    ///<return refout="true"></return>
    /**
     * 
     * @param mixed $v
     * @return *
     */
    protected function _access_offsetGet($v)
    {
        if (isset($this->m_envs[$v])) {
            $n = &$this->m_envs[$v];
            if ($n)
                return $n;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="i"></param>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $i
     * @param mixed $v
     */
    protected function _access_offsetSet($i, $v): void
    {
        if ($v === null)
            unset($this->m_envs[$i]);
        else
            $this->m_envs[$i] = $v;
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
     * 
     * @param mixed $i
     */
    protected function _access_offsetUnset($i): void
    {
        unset($this->m_envs[$i]);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function __serialize()
    {
        die("not allowed " . __CLASS__);
    }
    ///<summary>set localy variable</summary>
    /**
     * set localy variable
     */
    public function set($k, $v)
    {
        if ($v === null) {
            unset($this->m_envs[$k]);
        } else
            $this->m_envs[$k] = $v;
    }
    public function checkInArray($key, $value)
    {
        return is_array($t = $this->$key) && key_exists($value, $t);
    }
    public function unsetInArray($key, $value)
    {
        if (is_array($t = $this->$key)) {
            unset($t[$value]);
        }
        $this->$key = $t;
    }
    public function setInArray($key, $value)
    {
        if (!is_array($t = $this->$key)) {
            $t = [];
        }
        $t[$value] = 1;
        $this->$key = $t;
    }
    public function bypass_method(BaseController $ctrl, ?bool $bypass)
    {
        $this->set(get_class($ctrl) . '/bypass_method', $bypass);
    }

    public function get_file($file, $ext = IGK_VIEW_FILE_EXT)
    {
        $n = "." . strtolower($this->name());
        foreach ([$n, ""] as $k) {
            if (file_exists($f = $file . $k . $ext)) {
                return $f;
            }
        }
        return null;
    }

    /**
     * push data in environment data valiable
     * @param mixed $key 
     * @param mixed $value 
     * @return void 
     */
    public function push($key, $value)
    {

        $c = $this->get($key);
        if (!$c) {
            $c = [];
        }
        if (!is_array($c)) {
            throw new EnvironmentArrayException($key);
        }
        array_push($c, $value);
        $this->set($key, $c);
    }
    /**
     * pop environment array variable
     * @param string $key key to get
     * @return mixed 
     */
    public function pop($key)
    {
        $o = null;
        if (is_array($c = $this->get($key))) {
            $o = array_pop($c);
            $this->set($key, $c);
        }
        // if (\IGK\System\Html\HtmlLoadingContext::class == $key){
        //     $ref_count = count($c);
        //     igk_wln(__FILE__.":".__LINE__ , "Key : ".$key . " ::POP:: count " .$ref_count); 
        // }
        return $o;
    }
    /**
     * get the last environment storage
     * @param mixed $key 
     * @return mixed 
     * @throws IGKException 
     */
    public function last($key)
    {
        if (is_array($c = $this->get($key))) {
            return igk_getv($c, count($c) - 1);
        }
    }
    /**
     * get the first environment storage
     * @param mixed $key 
     * @return mixed 
     * @throws IGKException 
     */
    public function first($key)
    {
        if (is_array($c = $this->get($key))) {
            return igk_getv($c, 0);
        }
    }
    ////<summary>get instance of create array </summary>
    /**
     * get instance of create array 
     * @param mixed $key 
     * @return mixed 
     */
    public function &createArray($key)
    {
        $c = &$this->get($key);
        if (!$c) {
            $c = [];
            $this->m_envs[$key] = &$c;
        } else {
            if (!is_array($c)) {
                die(__("Present value is not registrated as an array:[{0}]."));
            }
        }
        return $c;
    }

    /**
     * check if a value is present in array. registrated in invironment
     * @param mixed $key 
     * @param mixed $value 
     * @return bool 
     */
    public function isInArray($key, $value)
    {
        $c = $this->createArray($key);
        return in_array($value, $c);
    }
    /**
     * call before init app to load extra configuration file
     * @param array $config 
     * @return void 
     */
    public function setConfigFiles(array $config)
    {
        $this->setArray("extra_config", "configFiles", $config);
    }

    /**
     * get if allowed to resolv SQL data type 
     * @return bool 
     */
    public function getResolvSQLType()
    {
        return !defined("IGK_TEST_INIT");
    }
    /**
     * get controller info properties
     * @return mixed 
     */
    public function getControllerInfo()
    {
        return self::GetClassInstance("controller::info");
    }
    /**
     * get module info propeties
     * @return \IGK\System\Modules\ModuleManager 
     */
    public function getModulesManager()
    {
        return self::GetClassInstance("module:manager");
    }
    public function getComposerLoader()
    {
        return self::GetClassInstance("composer:loader");
    }
    /**
     * get application cookie name
     */
    public function getCookieName()
    {
        return $this->get('cookie_name', IGK_DEFAULT_APP_COOKIE_NAME);
    }
    /**
     * set application cookie Name 
     * @param string $name 
     * @return $this 
     */
    public function setCookieName(string $name)
    {
        $this->set('cookie_name', $name);
        return $this;
    }
    /**
     * get input for input handling
     * @return null|FakeInput 
     */
    public function RequestFakeJsonInput(): ?FakeInput
    {
        return $this->get(__FUNCTION__);
    }
    /**
     * set faker input data 
     * @param mixed $data 
     * @return void 
     */
    public function setFakerInputData(?string $data)
    {
        if (!is_null($data)) {
            $data =  new ServerFakerInput($data);
        }
        igk_environment()->FakerInput = $data;
    }
}
