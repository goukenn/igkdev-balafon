<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DispatcherService.php
// @date: 20230706 10:43:00
// @desc: dispatcher service 
namespace IGK\Actions;
use IGK\Controllers\BaseController;
use IGK\Services\IAppService;
use IGK\System\DependencyInjection\LifeTime;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IInjectable;
use IGKServices;
use ReflectionException;
use ReflectionMethod;

/**
 * dispatcher to handle method call 
 * @package IGK\Actions
 */
abstract class DispatcherService
{
    /**
    * Property: services.
    * @var mixed
    */
    static $sm_services = [];
    /**
    * Property: last init service.
    * @var mixed
    */
    private static $sm_last_initService;
    /**
    * Constant: init args.
    * @var mixed
    */
    const INIT_ARGS = '@args';
    /**
    * Constant: type precision.
    * @var mixed
    */
    const TYPE_PRECISION = '@precision';
    /**
    * auto generate doc.
    * @param mixed $rtype mixed type to inject
    * @return mixed
    */
    public static function CreateOrGetServiceInstance(BaseController $ctrl, $rtype, string $typecheck = IInjectable::class)
    {
        $arguments = null;
        $m = null;
        $c_tg = self::INIT_ARGS;
        $v_transient = false;
        if (is_array($rtype)) {
            $bind = igk_getv($rtype, self::TYPE_PRECISION);
            $nkey = $bind ?? self::GetFirstClassTypeFromArray($rtype, $ctrl); 
            if (is_null($nkey)){
                return null;
            }
            $m = (array)$rtype[$nkey];
            $arguments = igk_getv($m, $c_tg) ?? [];
            $v_transient = igk_getv($m, IGKServices::KEY_LIFETIME) ==  LifeTime::TRANSIENT;
            $rtype = $nkey;
            if ($arguments && !is_array($arguments)){
                $arguments = [$arguments];
            }
        }
        if (!$typecheck || is_string($rtype)) {
            if (!$typecheck){
                igk_die('typecheck is not defined');
            }
            (class_exists($rtype) && is_subclass_of($rtype, $typecheck)) || igk_die(
                sprintf('misconfiguration target type not injectable [%s]', $rtype)
            );
        }
        $p = DispatcherService::GetServiceInstance($ctrl, $rtype, $v_transient, $arguments);
        if ($m && $p && self::IsServiceNewInstance()) {
            unset($m[$c_tg]);
            DispatcherService::SetupServiceInstance($p, $m);
        }
        return $p;
    }
    /**
    * auto generate doc.
    * @param mixed $array
    * @return int|string|null
    */
    public static function GetFirstClassTypeFromArray($array){
        while(count($array)>0){
            $key = key($array);
            $q = array_shift($array);
            if (is_array($q) && class_exists($key)){
                return $key;
            }
        }
        return null;
    }
    /**
     * get register injectable or service
     * @param BaseController $ctrl 
     * @param string $class_name 
     * @param bool $transient 
     * @param mixed ...$args 
     * @return mixed 
     */
    public static function  GetServiceInstance(BaseController $ctrl, string $class_name, $transient=false,  ...$args)
    {
        self::$sm_last_initService = null;
        $key = $ctrl->name(igk_uri('/services/' . $class_name));
        if ($transient || !isset(self::$sm_services[$key])) {
            if (is_array($args) && (count($args) == 1)) {
                $args = $args[key($args)];
            }
            $obj = IGKServices::CreateServiceNewInstance(igk_sys_reflect_class($class_name), $args);
            if (!$transient){
                self::$sm_services[$key] = $obj;
            }
            self::$sm_last_initService = $obj;
            return $obj;
        }
        $m = igk_getv(self::$sm_services, $key);
        return $m;
    }
    /**
    * auto generate doc.
    * @return bool
    */
    public static function IsServiceNewInstance():bool{
        return !is_null(self::$sm_last_initService);
    }
    /**
     * setup service properties 
     * @param mixed $p 
     * @param mixed $m 
     * @return void 
     */
    public static function SetupServiceInstance($p, $m)
    {
        $m = $m ?? [];
        $fc_bindprop = function ($p, $m) {
            foreach ($m as $key => $value) {
                if (method_exists($p, $fc = 'set' . ucfirst($key))) {
                    if ($parameters = (new ReflectionMethod($p, $fc))->getParameters()){
                        $arguments = Dispatcher::GetInjectArgsByParameters($parameters, [$value]);     
                        $value = array_shift($arguments);
                    }
                    $p->$fc($value);
                } else if (property_exists($p, $key)) {
                    $p->$key = $value;
                }
            }
        };
        if ($p instanceof IAppService) {
            $cnf = self::ConfigPropertyList($p->getConfigurableProperties());
            $fc_bindprop($p, igk_extract_assoc($m, $cnf)); 
        } else {
            $fc_bindprop($p, $m); 
        }
    }
    /**
     * transform to properties list 
     * @param mixed $p 
     * @return mixed[] 
     */
    public static function ConfigPropertyList($p){
        $o = [];
        foreach(array_keys($p) as $t){
            if (is_numeric($t)){
                $t = $p[$t];
            }
            $o[] = $t;
        }
        return $o;
    }
}