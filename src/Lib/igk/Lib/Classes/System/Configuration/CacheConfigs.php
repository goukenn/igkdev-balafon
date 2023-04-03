<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CacheConfigs.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Configuration;

use AppBootstrapController;
use IGK\Controllers\BaseController;
use IGK\Helper\ControllerHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKException;
use ReflectionException;
use stdClass;

require_once IGK_LIB_CLASSES_DIR . "/Helper/ControllerHelper.php";

/**
 * cache configuration options and special setting help reduce loading speed. 
 * @package IGK\System\Configuration
 */
final class CacheConfigs
{
    private static $sm_instance;
    /**
     * cache configuration options
     * @var mixed
     */
    private $cacheOptions;

    /**
     * store require change
     * @var bool
     */
    private $changed = false;

    private $m_update_references = [];

    private $m_changed_prop = [];
    /**
     * store file mtime
     * @var mixed
     */
    private $mtime;

    private $config_times = [];

    public function getCacheFile()
    {
        return igk_io_cachedir() . "/.configs.cache";
    }
    private function __construct()
    {
    }
    public function __get($n){
        igk_die("try access ". $n);
    }
    public function __set($n, $v){
        igk_die("set not allowed ".$n);
    }
    /**
     * get instance
     * @return static
     */
    public static function getInstance()
    {
        if (self::$sm_instance == null) {
            $i = self::$sm_instance = new self();
            if (file_exists($file = $i->getCacheFile())) {
                if ((($g = unserialize(file_get_contents($file))) !== false) && is_array($g)) {
                    $i->config_times = $g[0];
                    $i->cacheOptions = $g[1];
                    $i->mtime = filemtime($file);
                } else {
                    $i->cacheOptions = new stdClass;
                    $i->config_times = [];
                    $i->mtime = 0;
                }
            } else {
                $i->cacheOptions = (object)[];
            }
            register_shutdown_function(function () {                
                self::storeCacheOptions();
            });
        }
        return self::$sm_instance;
    }
    /**
     * get cached options 
     * @param BaseController $controller 
     * @param mixed $name 
     * @param mixed $defaut 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetCachedOption(BaseController $controller, $name, $defaut = null)
    {
        $i = self::getInstance();
        $cnf = ControllerHelper::getConfigFile($controller) ?? igk_die("configure file is missing");
        $cftime = @filemtime($cnf);
        $update = false;
        $binhash = $cnf; 
     

        if (isset($i->m_changed_prop[$binhash])) {
            $inf = $i->m_changed_prop[$binhash];
            if ($inf['cftime'] == $cftime) {
                $key = self::_GetKey($controller, $name);
                //retrieve from cache
                if (isset($inf['keys'][$key]) && self::_GetCacheValue($i, $controller, $name, $defaut, $value)) {
                    return $value;
                }
            }
        } else {
            $i->m_changed_prop[$binhash] = ['cftime' => null, 'keys'=>[]];
        }
        if (!isset($i->config_times[$binhash])) {
            $update = true;
            $i->config_times[$binhash] = max($cftime, $i->mtime);
        } else {
            $mtime = $i->config_times[$binhash];
            $diff = $cftime - $mtime;
            $update = $diff != 0; //$cftime > $mtime;
            if ($update) {
                $i->m_update_references[$binhash] = $cftime;
                $i->changed = true;  
            }
        }

        if (!$update) {
            if (self::_GetCacheValue($i, $controller, $name, $defaut, $value)) {
                return $value;
            }
        }
        // $controller->getConfigs()->storeConfig();    
        // convert boolean value                 
        $v = $controller->getConfigs()->get($name, $defaut);
        // + | --------------------------------------------------------------------
        // + | get boolean value
        // + |       
        if ($v && is_string($v) && in_array(strtolower($v), ['true', 'false', '1', '0'])) {
            $v = (bool)preg_match("/(true|1)/i", $v);
        }
        $i->m_changed_prop[$binhash]['cftime'] = $cftime;
        $i->m_changed_prop[$binhash]['keys'][self::_GetKey($controller, $name)] = 1;
        return self::registerCache($controller, $name, $v);
    }
    private static function _GetKey($controller, $name)
    {
        return get_class($controller) . "/" . $name;
    }
    private static function _GetCacheValue($i, $controller, $name, $default, &$value)
    {
        $options = igk_getv($i->cacheOptions, get_class($controller));
        $keyname = strtolower(igk_environment()->keyName());
        if ($options && property_exists($options, $keyname)) {
            if (($envkeys = $options->$keyname) && property_exists($envkeys, $name)) {
                $value =  igk_getv($envkeys, $name, $default);
                return true;
            }
            // return $defaut;
        } else if ($options && property_exists($options, $name)) {
            $value = igk_getv($options, $name, $default);
            return true;
        }
        return false;
    }
    /**
     * replace login service
     * @param BaseController $controller 
     * @param mixed $name 
     * @param mixed $value 
     * @return void 
     */
    public static function SetCachedOption(BaseController $controller, $name, $value)
    {
        $i = self::getInstance();
        $options = igk_getv($i->cacheOptions, get_class($controller));
        $keyname = strtolower(igk_environment()->keyName());
        if ($options && property_exists($options, $keyname)) {
            if (($envkeys = $options->$keyname) && property_exists($envkeys, $name)) {
                $envkeys->$name = $value;
                return true;
            }
            // return $defaut;
        } else if ($options) {
            $options->$name = $value;
            return true;
        }
    }
    public static function registerCache(BaseController $controller, $name, $value)
    {
        $i = self::getInstance();
        $cl =  get_class($controller);
        $keyname = strtolower(igk_environment()->keyName());
        if (!($options = igk_getv($i->cacheOptions, $cl))) {
            $options = new stdClass();
            $i->cacheOptions->$cl = $options;
        }
        if (!property_exists($options,  $keyname)) {
            $options->$keyname = new stdClass();
        }
        $options->$keyname->$name = $value;
        $i->changed = true;
        return $value;
    }
    /**
     * store cache option
     * @return void 
     * @throws IGKException 
     */
    public static function storeCacheOptions()
    {
        $i = self::getInstance();
        if (!defined("IGK_TEST_INIT") && $i->changed) {
            //--------------------------------------------------------- 
            // + |disable service worker storage
            //---------------------------------------------------------         
            if (igk_server()->HTTP_SEC_FETCH_DEST == 'serviceworker'){
                $i->change = false;
                return;
            }    
            foreach ($i->m_update_references as $k => $v) {
                $i->config_times[$k] = $v;
            }
            igk_io_w2file($i->getCacheFile(), serialize([$i->config_times, $i->cacheOptions]));
            $i->changed = false;
        }
    }

    /**
     * get registrated cache setting
     * @param mixed $name 
     * @param mixed $key 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetCachedSetting($name, $key, $default = null)
    {
        if (defined("IGK_TEST_INIT"))
            return null;

        $options = igk_getv(self::getInstance()->cacheOptions, $name);
        if ($options) {
            return igk_getv($options, $key, $default);
        }
        return null;
    }
    /**
     * register config cache setting
     * @param mixed $name 
     * @param mixed $key 
     * @param mixed $value 
     * @return void 
     * @throws IGKException 
     */
    public static function RegisterCacheSetting(string $name, string $key, $value)
    {
        $options = igk_getv(self::getInstance()->cacheOptions, $name);
        if (!$options) {
            $options = new \stdClass();
            self::getInstance()->cacheOptions->$name =  $options;
        }
        $options->$key = $value;
        self::getInstance()->changed = true;
        igk_trace();
        igk_wln_e(__METHOD__, $name, $key);
    }
}
