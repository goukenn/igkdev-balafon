<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKServices.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\Controllers\ServiceController;
use IGK\Services\IAppService;
require_once __DIR__ . "/IService.php";
class IGKServices
{
    static $sm_instance;
    private $m_services = [];
    // + | --------------------------------------------------------------------
    // + | service name
    // + |
    const PRINTER = "Printer";
    const MAPPING_SERVICE = "MappingService";
    // private $changed;
    // public static function FileCache(){
    //     return igk_io_cachedir()."/.services.cache";
    // }
    public function __get($name)
    {
        return igk_getv($this->m_services, $name);
    }
    public function __set($name, ?IAppService $service = null)
    {
        if ($service == null) {
            unset($this->m_services[$name]);
            return;
        }
        $this->m_services[$name] = $service;
    } 
    public static function getInstance()
    {
        if (self::$sm_instance === null) {
            self::$sm_instance = new self();
        }
        return self::$sm_instance;
    }
    /**
     * get service 
     * @param string $serviceName 
     * @return mixed 
     * @throws Exception 
     */
    public static function Get(string $serviceName)
    {
        $i = self::getInstance();
        $l = igk_getv($i->$serviceName, "instance");
        if ($l){
            return $l;
        }
        $className = igk_getv($i->$serviceName, '@def');
        if ($className){
        self::_InitServiceInstance($i, $serviceName, $className);
        $l = igk_getv($i->$serviceName, "instance");
        return $l;
        }
        return null;
    }
    /**
     * register service.
     * @param string $serviceName 
     * @param string $className a class that implement IAppService
     * @return void 
     * @throws IGKException 
     */
    public static function Register(string $serviceName, string $className)
    {
        $instance = self::getInstance();
        if (class_exists($className) && is_subclass_of($className, IAppService::class)) {
            if (!($c = igk_getv($instance->m_services, $serviceName)) || key_exists('@def', $c) || (get_class($c["instance"]) != $className)) {
                $instance->m_services[$serviceName] = ['@def'=>$className];
            }
        }
        return false;
    }
    private static function _InitServiceInstance($instance, $serviceName, $className){
        static $initializing; 
        static $configuration;
        if (is_null($configuration))
            $configuration = include igk_io_sys_datadir().'/services.php' ; // igk_app()->getServiceConfig();
        if (is_null($initializing)){
            $initializing = [];
        }
        if (isset($initializing[$className])){
            return $initializing[$className];
        }
        $cl = new $className();
        $initializing[$className] = $cl;
        if ($cl->init(igk_getv($configuration, $serviceName))) {
            $file = (igk_sys_reflect_class($className))->getFileName();
            $instance->m_services[$serviceName] = [
                "instance" => $cl,
                "l" => $file
            ];
            // $instance->changed = true;
            ServiceController::register($className, igk_io_collapse_path($file));
        }
        unset($initializing[$className]);
    }
    /**
     * retrieve services 
     * @return array 
     */
    public function services(){
        return $this->m_services;
    }
}
 