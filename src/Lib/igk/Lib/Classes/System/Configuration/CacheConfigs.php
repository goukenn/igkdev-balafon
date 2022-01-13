<?php
namespace IGK\System\Configuration;

use AppBootstrapController;
use IGK\Controllers\BaseController;
use IGKException;
use stdClass;

/**
 * cache configuration options and special setting help reduce loading speed. 
 * @package IGK\System\Configuration
 */
final class CacheConfigs{
    private static $sm_instance; 
    /**
     * cache configuration options
     * @var mixed
     */
    private $cacheOptions;

    private $changed;

    private $mtime;

    public function getCacheFile(){
        return igk_io_cachedir()."/.configs.cache";
    }
    private function __construct()
    { 
    }
    public static function getInstance(){
        if (self::$sm_instance == null){
            self::$sm_instance = new self();
            if (file_exists($file = self::$sm_instance->getCacheFile())){
                self::$sm_instance->cacheOptions = unserialize(file_get_contents($file));
                self::$sm_instance->mtime = filemtime($file);
            } else {
                self::$sm_instance->cacheOptions = (object)[];
            }
            register_shutdown_function(function(){
                self::storeCacheOptions();
            });
        }
        return self::$sm_instance;
    }
    public static function GetCachedOption(BaseController $controller, $name, $defaut=null){
        $i = self::getInstance();       
        $cnf = $controller->getConfigFile();
        if (!($i->mtime < filemtime($cnf))){
            $options = igk_getv($i->cacheOptions, get_class($controller)); 
            $keyname = strtolower(igk_environment()->keyName()); 
            if ($options && property_exists($options, $keyname)){
                if ($envkeys = $options->$keyname){
                    return igk_getv($envkeys, $name, $defaut);
                }
                return $defaut;
            }
        }   
        $v = $controller->getConfigs()->get($name, $defaut);
        return self::registerCache($controller, $name, $v);
    }
    public static function registerCache(BaseController $controller, $name, $value){
        $cl =  get_class($controller);

      $keyname = strtolower(igk_environment()->keyName());
 
        if ( ! ($options = igk_getv(self::getInstance()->cacheOptions,$cl))){
            $options = new stdClass(); 
            self::getInstance()->cacheOptions->$cl = $options;
        }
        if (! property_exists($options,  $keyname)){
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
    public static function storeCacheOptions(){
        if (self::getInstance()->changed){
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
    public static function GetCachedSetting($name, $key, $default=null){
        $options = igk_getv(self::getInstance()->cacheOptions, $name);
        if ($options){
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
    public static function RegisterCacheSetting(string $name, string $key, $value){
        $options = igk_getv(self::getInstance()->cacheOptions, $name);
        if (!$options){
            $options = new \stdClass();
            self::getInstance()->cacheOptions->$name =  $options;
        }
        $options->$key = $value;
        self::getInstance()->changed = true;
    }
}