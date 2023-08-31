<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ModuleManager.php
// @date: 20220829 09:41:42
// @desc: 

namespace IGK\System\Modules;

use IGK\Controllers\ApplicationModuleController;
use IGK\System\Controllers\ApplicationModules;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Modules\Helpers\Utility;
use IGK\System\Regex\Replacement;
use IGKException;
use IGKHtmlDoc;

use function PHPSTORM_META\map;

/**
 * manager module
 * 
 */
class ModuleManager
{
    /**
     * @var array
     */
    private $m_modules;

    private $m_boot_modules = [];
    /**
     * 
     * @var ModuleInitializer
     */
    private $m_init;

    public function __construct()
    {
        $this->m_modules =  & igk_environment()->require_modules();
        $this->m_init = $this->_createModuleInitializer();
    }
    /**
     * reset the loaded module and return previous backup
     * @return array 
     */
    public function reset()
    {
        $bck = array_combine(array_keys($this->m_modules), array_values($this->m_modules));
        $this->m_modules = [];
        $this->m_init->reset();
        return $bck;
    }
    public function restore(array $tab)
    {
        $this->m_modules = $tab;
        foreach ($tab as $value) {
            if ($value instanceof ApplicationModuleController) {
                $path = $value->getName();
                $this->m_init->register($path, $value);
            }
        }
    }
    /**
     * get reference to modules list
     * @return array 
     */
    public function &get()
    {
        return $this->m_modules;
    }
    public function count()
    {
        return igk_count($this->m_modules);
    }
    /**
     * return initialized modules 
     * @return ModuleInitializer 
     */
    public function init()
    {
        if (is_null($this->m_init)) {
            die("initializer not created");
        }
        return $this->m_init;
    }
    /**
     * create module inistializer
     * @return ModuleInitializer 
     */
    protected function _createModuleInitializer()
    {
        return new ModuleInitializer;
    }
    /**
     * get installed modules
     * @return null|array 
     * @throws IGKException 
     */
    public static function GetInstalledModules(): ?array
    {  
        $d = ApplicationModules::GetCacheFile();
        if (!file_exists($d)) {
            return self::_InitModules();
        }
        $cf = json_decode(igk_io_read_allfile($d));
        return (array)$cf;
    }
    private static function _InitModules(){
        $d = ApplicationModules::GetCacheFile();
        $modir = igk_get_module_dir();
        $ln = strlen($modir) + 1;
        $modules = igk_io_getfiles($modir, Replacement::RegexExpressionFromString("/".ApplicationModuleController::CONF_MODULE."$"));
        $tlist = [];
        if ($modules) {
            foreach ($modules as $f) {
                $name = self::_SanitizeName(substr(dirname($f), $ln));
                $obj = json_decode(file_get_contents($f));
  
                if ($obj && igk_is_valid_module_info($obj)) {
                    if ($obj->name != $name) {
                        igk_ilog("module not a valid name :" . $name . " vs " . $obj->name);
                    }
                    $tlist[$name] = $obj;
                }
            }
        }
        ksort($tlist);
        if (!defined('IGK_NO_LIB_CACHE')) {
            igk_io_w2file($d, json_encode($tlist, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        return $tlist;
    }
    /**
     * help sanitize name
     * @param string $dirname 
     * @return string|string[]|null 
     */
    private static function _SanitizeName(string $dirname){
        return Utility::SanitizeName($dirname);
    }
    public static function GetAutoloadModules(): ?array
    {
        $manager = igk_environment()->getModulesManager();
        if ($manager instanceof self)
            return $manager->m_boot_modules;
        return $manager->getAutoloadModules();
    }
    /**
     * retrieve required modules
     * @return null|array 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public static function GetRequiredModules(): ?array
    {
        if ($mod = igk_environment()->require_modules()) {
            unset($mod['::files']);
        }
        return $mod;
    }
    /**
     * bootstrap modules
     * @return void 
     */
    public static function Bootstrap()
    { 
        $boot_cache = igk_io_cachedir()."/.modules.boot.cache";
        if (file_exists($boot_cache)){
            if (($seri = unserialize(file_get_contents($boot_cache)))!==false){
                foreach($seri as $k){
                    self::_BootModule($k);
                }
                return;
            }
        }

        $f = ApplicationModules::GetCacheFile();
        if (file_exists($f)) {
            $tab = (array)json_decode(file_get_contents($f));
        } else {
            $tab = self::GetInstalledModules();
        } 
        if ($tab) {
            $info = array_filter(array_map(function ($a) {
                if (igk_getv($a, 'autoload')){
                    self::_BootModule($a->name);
                    return $a;
                }
                return null;
            }, $tab));

            igk_io_w2file($boot_cache, serialize(array_keys($info)));
        }
    }
    private static function _BootModule($n){
        $mod = igk_require_module($n, function () {
            return true;
        });
        $mod->boot = true;
        $manager = igk_environment()->getModulesManager();
        $manager->registerBoot($mod);
        return $mod;

    }
    /**
     * register boot module
     * @param ApplicationModuleController $module 
     * @return bool 
     */
    public function registerBoot(ApplicationModuleController $module):bool{
        if (array_search($module, $this->m_boot_modules)===false){
            $this->m_boot_modules[] = $module;
            return true;
        }
        return false;
    }
    /**
     * initialize document
     * @param IGKHtmlDoc $doc 
     * @param ApplicationModuleController $module 
     * @return void 
     */
    public static function InitDoc(IGKHtmlDoc $doc, ApplicationModuleController $module){
        if ($module->boot){
            $module->boot = false;
            $module->initDoc($doc);
        }
    }
    /**
     * reset module caches
     * @return array 
     * @throws IGKException 
     */
    public static function ResetModuleCache(){
        @unlink(ApplicationModules::GetCacheFile());
        return self::_InitModules();
    }
}
