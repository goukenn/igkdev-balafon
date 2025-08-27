<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKServices.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\Controllers\ServiceController;
use IGK\Services\IAppService;
use IGK\System\Core\ListOfCoreServices;

require_once __DIR__ . "/IService.php";
class IGKServices extends ListOfCoreServices
{
    static $sm_instance;
    private $m_services = []; 
    /**
     * service configurate file
     * @return string 
     */
    public static function ConfigurationFile(){
        return igk_configs()->get('service_configuration_file') ?? igk_io_sys_datadir().'/services.php';
    }
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
        $sn = $serviceName;
        self::_GetContainer($sn, $container, $path);
        if ($container) $sn = $path;
        $v_isn = $i->{$sn};
        $l = igk_getv($v_isn, "instance");
        if ($l){
            return $l;
        }
        $className = igk_getv($v_isn, '@def');
        if ($className){
            self::_InitServiceInstance($i, $sn, $className);
            $l = igk_getv($i->{$sn}, "instance");

            if ($container){
                $ext = igk_io_path_ext($serviceName);
                if (is_null($v_icontainer = igk_getv($container,'instance'))){
                    $v_icontainer = self::Get(substr($serviceName, 0, stripos($serviceName, '.')));
                }
                $v_icontainer->register($ext, $l);
            }
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
            self::_GetContainer($serviceName, $container, $path);
           
            if (!($c = igk_getv($instance->m_services, $serviceName)) || key_exists('@def', $c) || (get_class($c["instance"]) != $className)) {
                if (is_null($container))
                    $instance->m_services[$serviceName] = ['@def'=>$className];
                else{
                    $instance->m_services[$path] = ['@def'=>$className]; 
                }
            }
        }
        return false;
    }
    private static function _GetContainer(string $n, & $container, & $path){
        $container = null;
        $path = '';
        $p = explode('.', $n);
        $n = array_pop($p);
        $ch = '';
        $instance = self::getInstance();
        while(count($p)>0){
            $q = array_shift($p);
            $path .= $ch.$q;
            if (is_null($container)){
                $container = igk_getv($instance->m_services, $q) ?? igk_die('missing root service container. '.$q);
            }else{
                $container = $container->get($q) ?? igk_die('missing service-container in !'.$path);
            }
            $ch = '/';
        }
        $path = $path.'::'.$n;
    }
    /**
     * 
     * @param mixed $instance 
     * @param string $serviceName service path 
     * @param string $className 
     * @return mixed|void 
     * @throws IGKException 
     * @throws Exception 
     */
    private static function _InitServiceInstance($instance, string $serviceName, string $className){
        static $initializing; 
        static $configuration;
        if (is_null($configuration))
            $configuration = include self::ConfigurationFile(); 
        if (is_null($initializing)){
            $initializing = [];
        }
        if (isset($initializing[$className])){
            return $initializing[$className];
        }
        $cl = new $className();
        $initializing[$className] = $cl;
        $config_key = str_replace('/','.',  str_replace('::', '.', $serviceName));

        if ($cl->init(igk_getv($configuration, $config_key))) {
            $file = (igk_sys_reflect_class($className))->getFileName();
            $instance->m_services[$serviceName] = [
                "instance" => $cl,
                "file" => $file
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
 