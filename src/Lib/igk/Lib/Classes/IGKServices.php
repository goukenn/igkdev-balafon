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
    public static function Get(string $serviceName)
    {
        return igk_getv(self::getInstance()->$serviceName, "instance");
    }

    /**
     * register service
     * @param string $serviceName 
     * @param string $className 
     * @return void 
     * @throws IGKException 
     */
    public static function Register(string $serviceName, string $className)
    {
        $instance = self::getInstance();
        if (class_exists($className) && is_subclass_of($className, IAppService::class)) {

            if (!($c = igk_getv($instance->m_services, $serviceName)) || (get_class($c["instance"]) != $className)) {
                $cl = new $className();
                if ($cl->init()) {
                    $file = (igk_sys_reflect_class($className))->getFileName();
                    $instance->m_services[$serviceName] = [
                        "instance" => $cl,
                        "l" => $file
                    ];
                    // $instance->changed = true;
                    ServiceController::register($className, igk_io_collapse_path($file));
                }
            }
        }
    }
}
/*

class ServiceHandler{
    var $tab;
    public function __construct(& $tab)
    {
        $this->tab = $tab;
        error_log("sleep handle ..... construct");
    }
    
    public function serialize(){
        return "{'seri':'data'}";
    }
    public function unserialize($s){

    }
}


*/