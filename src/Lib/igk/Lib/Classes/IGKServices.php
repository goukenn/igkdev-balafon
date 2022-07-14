<?php

use IGK\Controllers\ServiceController;
use IGK\IService;

require_once __DIR__ . "/IService.php";

class IGKServices
{
    static $sm_instance;

    private $m_services = [];

    // private $changed;
    // public static function FileCache(){
    //     return igk_io_cachedir()."/.services.cache";
    // }

    public function __get($name)
    {
        return igk_getv($this->m_services, $name);
    }
    public function __set($name, ?IService $service = null)
    {
        if ($service == null) {
            unset($this->m_services[$name]);
            return;
        }
        $this->m_services[$name] = $service;
    }

    private function __construct()    
    {
        $this->m_services = [];  

        // igk_reg_hook(IGKEvents::HOOK_SHUTDOWN, function()use(& $tab){
        //     igk_wln_e("shutdown call ");
        //     $file = self::FileCache();
        //     if ($this->changed){            
        //         $m = "";
        //         foreach($this->m_services as $k=>$v){
        //             $l = igk_io_collapse_path($v["l"]);
        //             $m .= "\"{$k}\" = \"{$l}\", \n";
        //             $tab[$k] = $l;
        //         }
        //         igk_io_w2file($file, "return [\n".$m."];");
        //     }
        // });
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
        if (class_exists($className) && is_subclass_of($className, IService::class)) {

            if (!($c = igk_getv($instance->m_services, $serviceName)) || (get_class($c["instance"]) != $className)) {
                $cl = new $className();
                if ($cl->init()) {
                    $file = (new ReflectionClass($className))->getFileName();
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
