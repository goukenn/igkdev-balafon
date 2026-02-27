<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKServices.php
// @date: 20220803 13:48:54
// @desc: 

use IGK\Actions\Dispatcher;
use IGK\Actions\DispatcherService;
use IGK\Controllers\ServiceController;
use IGK\Services\IAppService;
use IGK\Services\IAppServiceContainer;
use IGK\System\Core\ListOfCoreServices;
use IGK\System\DependencyInjection\LifeTime;
use function igk_resources_gets as __;

require_once __DIR__ . "/IService.php";

/**
* Igkservices.
*/
class IGKServices extends ListOfCoreServices
{

    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;

    /**
    * Property: services.
    * @var mixed
    */
    private $m_services = [];
    /**
     * store init transient service 
     * @var array
     */
    private $m_transients = [];

    /**
    * Constant: key def.
    * @var mixed
    */
    const KEY_DEF = '@def';

    /**
    * Constant: key lifetime.
    * @var mixed
    */
    const KEY_LIFETIME = '@lifetime';

    /**
    * Constant: init args.
    * @var mixed
    */
    const INIT_ARGS = DispatcherService::INIT_ARGS;

    /**
    * Constant: key instance.
    * @var mixed
    */
    const KEY_INSTANCE = 'instance';

    /**
    * Constant: path separator.
    * @var mixed
    */
    const PATH_SEPARATOR = '::';

    /**
    * Property: init def.
    * @var mixed
    */
    private static $sm_initDef;

    /**
    * Sets Definition.
    * @param mixed $n
    */

    public static function SetDefinition($n)
    {
        self::$sm_initDef = $n;
    }
    /**
     * service configurate file
     * @return string 
     */

    public static function ConfigurationFile()
    {
        return igk_configs()->get('service_configuration_file') ?? igk_io_sys_datadir() . '/services.php';
    }
    // private $changed;
    // public static function FileCache(){
    //     return igk_io_cachedir()."/.services.cache";
    // }

    /**
    * Magic getter for dynamic properties.
    * @param mixed $name
    */

    public function __get($name)
    {
        return igk_getv($this->m_services, $name);
    }

    /**
    * Magic setter for dynamic properties.
    * @param mixed $name
    * @param null|IAppService $service
    */

    public function __set($name, ?IAppService $service = null)
    {
        if ($service == null) {
            unset($this->m_services[$name]);
            return;
        }
        $this->m_services[$name] = $service;
    }
    /**
     * retrieve the core service instances 
     * @return static 
     */

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
        $v_key = self::KEY_INSTANCE;
        self::_GetContainer($sn, $container, $path);
        if ($container) $sn = $path;
        $v_isn = $i->{$sn};
        $l = igk_getv($v_isn,  $v_key);
        $v_transient = false;
        if ($l){
            if (!($v_isn = igk_getv($i->m_transients, $serviceName))){
                // just return a sigleton value
                return $l;
            }
            $v_transient = true;
        }
        $className = igk_getv($v_isn, self::KEY_DEF);
        if ($className){
            if (!$v_transient && igk_getv($v_isn, self::KEY_LIFETIME) == LifeTime::TRANSIENT){
                $i->m_transients[$serviceName] = $v_isn;
            }
            self::_InitServiceInstance($i, $sn, $className);
            $l = igk_getv($i->{$sn},  $v_key );

            if ($container) {
                $ext = igk_io_path_ext($serviceName);
                if (is_null($v_icontainer = igk_getv($container,  $v_key ))) {
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
     * @return bool
     * @throws IGKException 
     */

    public static function Register(string $serviceName, string $className, ?array $args = null, $life_time = LifeTime::SINGLETON): bool
    {
        $instance = self::getInstance();
        if (class_exists($className) && is_subclass_of($className, IAppService::class)) {
            self::_GetContainer($serviceName, $container, $path);
            $v_kdef = self::KEY_DEF;

            if (!($c = igk_getv($instance->m_services, $serviceName)) || key_exists($v_kdef, $c) || (get_class($c[self::KEY_INSTANCE]) != $className)) {
                $cf = [$v_kdef => $className, self::KEY_LIFETIME=>$life_time];
                if ($args) {
                    $cf[$className] = self::_InitDefArgs($args);
                }
                if (is_null($container))
                    $instance->m_services[$serviceName] = $cf;
                else {
                    $instance->m_services[$path] = $cf;
                }
                return true;
            }
        }
        return false;
    }
    /**
     * init service definition array
     * @param array $targs 
     * @return array 
     */
    private static function _InitDefArgs(array $targs): array
    {
        $out = [];
        $args = [];
        $props = [];
        foreach (array_keys($targs) as $k) {
            if (is_numeric($k)) {
                $args[] = $targs[$k];
            } else {
                $props[$k] = $targs[$k];
            }
        }
        if ($args)
            $out[DispatcherService::INIT_ARGS] = $args;
        if ($props) {
            $out = array_merge($out, $props);
        }
        return $out;
    }
    /**
     * retrieve service container 
     * @param string $n 
     * @param mixed &$container 
     * @param mixed &$path 
     * @return void 
     * @throws Exception 
     */
    private static function _GetContainer(string $n, &$container, &$path)
    {
        $container = null;
        $path = '';
        $p = explode('.', $n);
        $n = array_pop($p);
        $ch = '';
        $instance = self::getInstance();
        while (count($p) > 0) {
            $q = array_shift($p);
            $path .= $ch . $q;
            if (is_null($container)) {
                $container = igk_getv($instance->m_services, $q) ?? igk_die('missing root service container. ' . $q);
            } else {
                $container = $container->get($q) ?? igk_die('missing service-container in !' . $path);
            }
            $ch = '/';
        }

        if (empty($path)){
            $path = $n;
        }else{
            $path = $path . self::PATH_SEPARATOR . $n;
        }
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
    private static function _InitServiceInstance($instance, string $serviceName, string $className)
    {
        static $initializing;
        static $configuration;
        if (is_null($configuration))
            $configuration = include self::ConfigurationFile();
        if (is_null($initializing)) {
            $initializing = [];
        }
        if (isset($initializing[$className])) {
            return $initializing[$className];
        }
        $ci = self::$sm_initDef;
        if ($ci) {
            if ($v_tci = igk_getv($configuration, $serviceName)) {
                if (!is_array($v_tci)) {
                    $v_tci = [$v_tci];
                } 
            }  
            $configuration[$serviceName] = $v_tci ? array_merge($v_tci, $ci) : $ci;
        }

        $config_key = str_replace('/', '.',  str_replace('::', '.', $serviceName));
        $gkey = sprintf('%s/%s', $config_key, $className);
        $args = self::_GetConfigurationServiceArgs($configuration, $gkey);
        if ($args && !is_array($args)){
            $args = [$args];
        }

        $v_refcl = igk_sys_reflect_class($className);        
        $cl = self::CreateServiceNewInstance($v_refcl, $args);       
        // $parameters = $v_refcl->getConstructor()->getParameters();        
        // $arguments = Dispatcher::GetInjectArgsByParameters($parameters, $args ?? []);        
        // $cl = $v_refcl->newInstanceArgs($arguments);
        $initializing[$className] = $cl;
        $cnf = self::_GetFallingConfiguration($configuration, $gkey);
        if ($cl->init($cnf)){
            $file = $v_refcl->getFileName();
            $instance->m_services[$serviceName] = [
                self::KEY_INSTANCE => $cl,
                "file" => $file
            ];
            if ($cl instanceof IAppServiceContainer){
                $cl->setName($serviceName);
            }
            ServiceController::register($className, igk_io_collapse_path($file));
        }
        unset($initializing[$className]);
    }
    /**
     * 
     * @param ReflectionClass $v_refclass 
     * @param null|array $args 
     * @return object|null 
     */

    public static function CreateServiceNewInstance(ReflectionClass $v_refclass, ?array $args){
        static $createedInstance;
        ($v_refclass->isAbstract()) && igk_die('class is abstract');
        if (is_null($createedInstance)){
            $createedInstance = [];
        }
        $classname = $v_refclass->getName();
        isset($createedInstance[$classname]) && igk_die(sprintf(__('recursive create service instance detected on %s'), $classname));
        $createedInstance[$classname] = 1;
        $ctr = $v_refclass->getConstructor();
        if ($ctr){
            $parameters = $ctr->getParameters();        
            $arguments = Dispatcher::GetInjectArgsByParameters($parameters, $args ?? []);        
            $cl = $v_refclass->newInstanceArgs($arguments);
        } else {
            $cl = new $classname();
        }
        unset($createedInstance[$classname]);
        return $cl;
    }
   
    /**
     * go up key 
     * @param string &$gkey 
     * @return bool 
     */
    private static function _GoKeyUp(string &$gkey): bool
    {
        $tab = explode('/', $gkey);
        array_pop($tab);
        if (!$tab) {
            return false;
        }
        $gkey = implode('/', $tab);
        return true;
    }
    /**
     * 
     * @param mixed $configuration 
     * @param string $gkey 
     * @return mixed 
     */
    private static function _GetFallingConfiguration($configuration, string $gkey)
    {
        $fc_unset_args = function($l): bool{
            unset($l[self::INIT_ARGS]);
            unset($l[self::KEY_LIFETIME]);
            if ($def = igk_getv($l, self::KEY_DEF)){
                unset($l[self::KEY_DEF]);
                unset($l[$def]);
            }
            return empty($l);
        };
        $l = null;
        if ($gkey) {
            while (!($l = igk_conf_get($configuration, $gkey)) || $fc_unset_args($l)) {
                if (!self::_GoKeyUp($gkey)) {
                    break;
                }
            }
        }
        return $l;
    }

    /**
    * auto generate doc.
    * @param string $gkey
    * @return mixed
    */
    private static function _GetConfigurationServiceArgs($configuration, string $gkey)
    {
        $l = null;
        if ($gkey) {
            while (!($l = igk_conf_get($configuration, sprintf('%s/%s', $gkey, DispatcherService::INIT_ARGS)))) {
                if (!self::_GoKeyUp($gkey)) {
                    break;
                }
            }
        }
        return $l;
    }
    /**
     * retrieve services 
     * @return array 
     */

    public function services(): array
    {
        if (is_null($this->m_services)){
            $this->clear();
        }
        return $this->m_services;
    }
    /**
     * clear loading service
     * @return void 
     */

    public function clear(){
        $this->m_services = [];
        $this->m_transients = [];
    }
}
