<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CacheConfigs.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Configuration;

use AppBootstrapController;
use IGK\Controllers\BaseController;
use IGK\Helper\ControllerHelper;
use IGKException;
use stdClass;

require_once IGK_LIB_CLASSES_DIR."/Helper/ControllerHelper.php";

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
    /**
     * store file mtime
     * @var mixed
     */
    private $mtime;

    public function getCacheFile()
    {
        return igk_io_cachedir() . "/.configs.cache";
    }
    private function __construct()
    {
    }
    public static function getInstance()
    {
        if (self::$sm_instance == null) {
            self::$sm_instance = new self();
            if (file_exists($file = self::$sm_instance->getCacheFile())) {
                if (($g = unserialize(file_get_contents($file))) !== false){
                    self::$sm_instance->cacheOptions = $g; 
                } else {
                    self::$sm_instance->cacheOptions = new stdClass;
                }
                self::$sm_instance->mtime = filemtime($file);
            } else {
                self::$sm_instance->cacheOptions = (object)[];
            }
            register_shutdown_function(function () {
                self::storeCacheOptions();
            });
        }
        return self::$sm_instance;
    }
    public static function GetCachedOption(BaseController $controller, $name, $defaut = null)
    {
        $i = self::getInstance();
        $cnf = ControllerHelper::getConfigFile($controller) ?? igk_die("configure file is missing");
        // if (!$i->changed || !($i->mtime < filemtime($cnf)))
        if (!($i->mtime < @filemtime($cnf))){
            $i->changed = false;
            $options = igk_getv($i->cacheOptions, get_class($controller));
            $keyname = strtolower(igk_environment()->keyName());
            if ($options && property_exists($options, $keyname)) {
                if (($envkeys = $options->$keyname) && property_exists($envkeys, $name)) {
                    return igk_getv($envkeys, $name, $defaut);
                }
                // return $defaut;
            } else if ($options && property_exists($options, $name)){
                return igk_getv($options, $name, $defaut);
            }
        }
        // $controller->getConfigs()->storeConfig();    
        // convert boolean value                 
        $v = $controller->getConfigs()->get($name, $defaut);
       // + | --------------------------------------------------------------------
       // + | get boolean value
       // + |       
        if ($v && in_array(strtolower($v), ['true', 'false','1','0'])){
            $v = (bool)preg_match("/(true|1)/i", $v);
        }
        return self::registerCache($controller, $name, $v);
    }
    /**
     * replace login service
     * @param BaseController $controller 
     * @param mixed $name 
     * @param mixed $value 
     * @return void 
     */
    public static function SetCachedOption(BaseController $controller, $name, $value){
        $i = self::getInstance();
        $options = igk_getv($i->cacheOptions, get_class($controller));
        $keyname = strtolower(igk_environment()->keyName());
        if ($options && property_exists($options, $keyname)) {
            if (($envkeys = $options->$keyname) && property_exists($envkeys, $name)) {
                $envkeys->$name = $value; 
                return true;
            }
            // return $defaut;
        } else if ($options){
                $options->$name= $value;
                return true;
        }
    }
    public static function registerCache(BaseController $controller, $name, $value)
    {
        $cl =  get_class($controller);
        $keyname = strtolower(igk_environment()->keyName());
        if (!($options = igk_getv(self::getInstance()->cacheOptions, $cl))) {
            $options = new stdClass();
            self::getInstance()->cacheOptions->$cl = $options;
        }
        if (!property_exists($options,  $keyname)) {
            $options->$keyname = new stdClass();
        }
        $options->$keyname->$name = $value;
        self::getInstance()->changed = true;
        return $value;
    }
    /**
     * store cache option
     * @return void 
     * @throws IGKException 
     */
    public static function storeCacheOptions()
    {
        if (!defined("IGK_TEST_INIT") && self::getInstance()->changed) {
            igk_io_w2file(self::getInstance()->getCacheFile(), serialize(self::getInstance()->cacheOptions));
            self::getInstance()->changed = false;
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
    }
}
