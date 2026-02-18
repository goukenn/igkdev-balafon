<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Dispatcher.php
// @date: 20220803 13:48:58
// @desc: action dispatcher 
namespace IGK\Actions;

use Closure;
use Exception;
use IGK\Actions\IActionProcessor;
use IGK\System\Exceptions\ActionNotFoundException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Http\Request;
use IGK\System\Http\RequestHeader;
use IGK\System\Http\RequestResponse;
use IGK\System\IInjectable;
use IGK\System\Regex\MatchPattern;
use IGK\System\Services\InjectorProvider;
use IGK\Actions\ActionBase;
use IGK\Controllers\ControllerParams;
use IGK\Controllers\SysDbController;
use IGK\Helper\ViewHelper;
use IGK\Models\Injectors\ModelBaseInjector;
use IGK\Models\ModelBase;
use IGK\Models\Users;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\OperationNotAllowedException;
use IGK\System\IInjectedArgHost;
use IGKException;
use IGKServices;
use IGKType;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use TypeError;
use function igk_resources_gets as __;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * default action dispactcher
 */
class Dispatcher implements IActionProcessor, IActionDispatcher
{
    const DISPATCH_METHOD = 'Dispatch';
    const INSTANCE = IGKServices::KEY_INSTANCE;
    /**
     * 
     * @var null|ActionBase|IActionProcessor|object
     */
    private $m_host;
    private static $sm_macro;
    private static $sm_matches = [
        "int" => MatchPattern::Int,
        "float" => MatchPattern::Float,
    ];
    ///<sumary>.ctr</summary>
    /**
     * .ctr
     * @param null|IGKActionBase $host 
     * @return void 
     */
    public function __construct(?ActionBase $host)
    {
        $this->m_host = $host;
    }
    /**
     * 
     * @param string $actionName 
     * @return void 
     */
    public function setBaseActionName(string $actionName)
    {
        $this->m_host->baseActionName = $actionName;
    }
    public function getBaseActionName(): string
    {
        return $this->m_host->baseActionName;
    }
    public function getController()
    {
        return $this->m_host ? $this->m_host->getController() : null;
    }
    public function getHost()
    {
        return $this->m_host;
    }
    public function skipVerbCheck(string $action_name)
    {
        $h = $this->getHost();
        if (method_exists($h, $fc = ucfirst(__FUNCTION__))) {
            return call_user_func_array([get_class($h), $fc], [$h, $action_name]);
        }
        return false;
    }
    /**
     * 
     * @param callable $fc 
     * @param mixed $args 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    protected static function _HandleDispatch(callable $fc, ...$args)
    {
        $g = new ReflectionFunction($fc);
        $args = self::GetInjectArgs($g, $args);
        try {
            return $fc(...$args);
        } catch (Exception $ex) {
            throw $ex;
        } catch (TypeError $ex) {
            // + | call to function but arguments injection no valid
            throw new OperationNotAllowedException('Dispatcher failed: ' . $ex->getMessage(), 405, $ex);
        }
    }
    public static function __callStatic($name, $args)
    {
        if (self::$sm_macro === null) {
            self::$sm_macro = [];
            self::$sm_macro[self::DISPATCH_METHOD] = function ($fc, ...$args) {
                return static::_HandleDispatch($fc, ...$args);
            };
        }
        if (is_callable($fc = igk_getv(self::$sm_macro, $name))) {
            return $fc(...$args);
        }
        return (new static(null))->$name(...$args);
    }
    public function invoke(string $name, ...$args)
    {
        return $this->__call($name, $args);
    }
    public function __call($name, $arguments)
    {
        $v_host = $this->m_host;
        if (
            method_exists($v_host, $name)
            && (!(new ReflectionMethod($v_host, $name))->isStatic())
            && ($fc = Closure::fromCallable([$v_host, $name])->bindTo($v_host))
        ) {
            // initialize replace uri 
            $v_host->getController()->{ControllerParams::REPLACE_URI} =
                $v_host->getDefaultEntryMethod() != $name;
            $targs = array_merge([$fc], $arguments);
            return self::__callStatic(self::DISPATCH_METHOD, $targs);
        } else {
            if ($v_host instanceof IActionProcessor) {
                return call_user_func_array(
                    [$this->m_host, '__call'],
                    [$name, $arguments]
                );
            }
        }
        throw new ActionNotFoundException($name);
    }
    /**
     * @param ReflectionFunction #Parameter#cd4a68c3 
     * @param IGK\Actions\ref #Parameter#ca4a640a 
     * @param mixed $args 
     * @return void 
     */
    public static function ResolvDispatchMethod(ReflectionFunctionAbstract $g, &$args)
    {
        $args = self::GetInjectArgs($g, $args);
    }
    /**
     * get argument to inject or dispatch
     * @param mixed $parameters 
     * @param mixed $args 
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetInjectArgsByParameters($parameters, $args, ?IInjectedArgHost $host = null)
    {
        $targs = [];
        self::_GetInjectedParameters($targs, $parameters, $args, $host);
        return $targs;
    }
    /**
     * get injected args
     * @param ReflectionFunctionAbstract $g 
     * @param mixed $args 
     * @param ?IInjectedArgHost $host host that will retreive parameters
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function GetInjectArgs(ReflectionFunctionAbstract $g, $args, ?IInjectedArgHost $host = null): array
    {
        $parameters = $g->getParameters();
        if (count($parameters) == 0) {
            return $args;
        }
        $targs = [];
        self::_GetInjectedParameters($targs, $parameters, $args, $host);
        return $targs;
    }
    /**
     * 
     * @param array $targs 
     * @param mixed $parameters 
     * @param mixed $args 
     * @param ?IInjectedArgHost $host injected argument host
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _GetInjectedParameters(array &$targs, $parameters, $args, ?IInjectedArgHost $host = null)
    {
        $targs = [];
        $injectors = InjectorProvider::GetInjectors();
        $v_host = $host ?? (\IGKApp::IsInit() ? (ViewHelper::CurrentCtrl() ?? SysDbController::ctrl()) : null);
        $v_injector = InjectorProvider::getInstance();
        $i = 0;
        $services = null;
        if ($v_host) {
            // + | --------------------------------------------------------------------
            // + | resolving services for injection
            // + |            
            self::LoadInjectableAndServices($services, $v_host);
        }
        // $v_inject = false;
        $v_is_debug = igk_is_debug() && igk_environment()->get('debug/dispatcher');
        foreach ($parameters as $k) {
            $v_is_debug && Logger::info(sprintf('update-dispatcher : %s next %s', $k, $i));

            $c = $arg = igk_getv($args, $i);
            $c_update_i = false;
            $v_precision = null;
            if (is_null($c) && $k->isDefaultValueAvailable()) {
                $c = $k->getDefaultValue();
            }
            if (($p = $k->getType()) && ($type = IGKType::GetName($p))) {
                if (is_object($c)) {
                    if ((get_class($c) == $type) || is_subclass_of($c, $type)) {
                        $targs[] = $c;
                        $i++;
                        continue;
                    }
                    igk_die(sprintf(__('argument %s not matching'), $i));
                }



                if ($type == 'string') {
                    $targs[] = $c;
                    $i++;
                    continue;
                }
                if ($type == "array") {
                    if (!is_array($c)) {
                        $c = $c ? explode(',', $c) : [];
                    }
                } else {
                    $pattern = igk_getv(self::$sm_matches, $type, ".+");
                    if (is_string($c) && $c && !preg_match_all("#^" . $pattern . "$#", $c)) {
                        throw new ArgumentTypeNotValidException($i);
                    }
                }
                // + | get inject table class printer service
                $v_primary = IGKType::IsPrimaryType($type);
                $v_injectable = !$v_primary && IGKType::IsInjectable($type);
                $v_ci = null;


                if (is_string($c) && $v_injectable && !$v_injector->injector($type) && class_exists($c) && is_subclass_of($c, $type)) {
                    $v_refcl = igk_sys_reflect_class($c);
                    $v_precision = $c;
                    $c_update_i = true;
                    if (!isset($services[$type])) {
                        $v_params = [];
                        $cl = IGKServices::CreateServiceNewInstance($v_refcl, $v_params);
                        $services[$type] = [
                            IGKServices::KEY_INSTANCE => $cl
                        ];
                    } else {
                        $c_instance = igk_getv($services[$type], self::INSTANCE);
                        if ($c_instance && (get_class($c_instance) != $c)) {
                            $v_ci = IGKServices::CreateServiceNewInstance($v_refcl, $v_params);
                        }
                    }
                }

                if (
                    $v_injectable &&
                    $services && isset($services[$type])
                ) {
                    if ($rtype = $services[$type]) {
                        // + | contains data 
                        unset($rtype[DispatcherService::TYPE_PRECISION]);
                        if (!$v_ci && $v_precision) {
                            if (!igk_getv($rtype, $v_precision)) {
                                $rtype[$v_precision] = [];
                            }
                            $rtype[DispatcherService::TYPE_PRECISION] = $v_precision;
                        }

                        $v_ci = $v_ci ?? self::GetInstanceServiceFromArrayDefinition($rtype, $type, $services);
                        // + | --------------------------------------------------------------------
                        // + | retrieve service instance definition
                        // + |  
                        if (is_null($v_ci) && !$v_precision && class_exists($type)) {
                            // pass resolve type to 
                            $rtype = [$type => $rtype];
                        }
                        $targs[] = $v_ci ?? DispatcherService::CreateOrGetServiceInstance($v_host, $rtype);
                        if ($c_update_i) {
                            $i++;
                        }
                        continue;
                    }
                }
                if (!$v_primary && class_exists($type)) {
                    if ($v_injectable && ($v_tc = self::_GetInjectable($type, $args))) {
                        // system injectable list 
                        $targs[] = $v_tc;
                        // $v_inject = true;
                        continue;
                    }
                    $j = igk_getv($injectors, $type, InjectorProvider::getInstance()->injector($type));
                    $j_allow_null =  $k->allowsNull();
                    if ($j && is_null($arg)) {
                        // + | --------------------------------------------------------------------
                        // + | auto inject parameter if null or allow null 
                        // + |                        
                        $c = null;
                        if ($j instanceof ModelBaseInjector) {
                            if (($ju = $j->getModel()) instanceof Users) {
                                ($u = $v_host->getUser()) && ($c = $u->model());
                            }
                        }
                        (!$j_allow_null) && is_null($c) && igk_die('null value not allowed');
                        $targs[] = $c;
                        $i++;
                        continue;
                    }
                    if ($j) {

                        if ($c = $j->resolve($arg, $p)) {
                            $targs[] = $c;
                            $i++;
                            continue;
                        } else {
                            $args = array_merge(array_slice($args, 0, $i), [null], array_slice($args, $i));
                        }
                    }
                } else if ($v_primary && is_null($c)) {
                    if ($k->isDefaultValueAvailable()) {
                        $c =  $k->getDefaultValue();
                    } else {
                        $c = preg_match("/(int|float|double|decimal)/i", $type) ? 0 : $c;
                    }
                }
            } else {
                if ($arg === null && $k->isDefaultValueAvailable()) {
                    $c = $k->getDefaultValue();
                }
            }
            $targs[] = $c;
            $i++;
        }
        if ($i < count($args)) {
            $targs = array_merge($targs, array_slice($args, $i));
        }
        return $targs;
    }

    /**
     * 
     * @param mixed &$services 
     * @return void 
     */
    protected static function _UpdateService(&$services)
    {
        $lbService = IGKServices::getInstance()->services();
        foreach ($lbService as $k => $m) {
            if (isset($services[$k])) {
                $g = $services[$k];
                $m = array_merge($m, $g);
                $services[$k] = $m;
            } else {
                $services[$k] = $m;
            }
        }
    }
    /**
     * retrive a service instance 
     * @param array $rtype 
     * @param mixed $type 
     * @return ?mixed 
     */
    public static function GetInstanceServiceFromArrayDefinition(array $rtype, $type)
    {
        $v_ci = null;
        if (isset($rtype[IGKServices::KEY_DEF])) {
            IGKServices::SetDefinition($rtype);
            $v_ci = IGKServices::Get($type);
            IGKServices::SetDefinition(null);
        } else if (isset($rtype[$i = IGKServices::KEY_INSTANCE])) {
            $v_ci = $rtype[$i];
        }
        return $v_ci;
    }
    /**
     * retrieve injectable from deispacther
     * @param mixed $class_name 
     * @param mixed $type 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetInjectTypeInstance($class_name)
    {
        return self::_GetInjectable($class_name, []);
    }
    /**
     * get global injectable 
     * @param mixed $type 
     * @param mixed $args 
     * @return mixed 
     */
    private static function _GetInjectable($type, $args)
    {
        static $injects = null;
        if ($injects === null) {
            $injects = [
                Request::class => function () {
                    $i = Request::getInstance();
                    $i->setParam(func_get_args());
                    return $i;
                },
                RequestHeader::class => new RequestHeader(),
                RequestResponse::class => RequestResponse::CreateResponse(),
            ];
            // extract injector server 
        }

        if (is_subclass_of($type, ModelBase::class)) {
            // use injector to register injection -
            return null;
        }
        if (!($m = igk_getv($injects, $type))) {
            $refclass = igk_sys_reflect_class($type);
            $m = IGKServices::CreateServiceNewInstance($refclass, $args);
            //$m = new $type();
            $injects[get_class($m)] = $m;
        }
        if (is_callable($m)) {
            return $m(...$args);
        }
        return $m;
    }
    /**
     * loading injectable 
     * @param array &$services 
     * @param mixed $v_host 
     * @return void 
     */
    public static function LoadInjectableAndServices(?array &$services, $v_host)
    {
        $thost = [$v_host];
        if (!($v_host instanceof SysDbController)) {
            array_unshift($thost, SysDbController::ctrl());
        }
        while (count($thost) > 0) {
            $v_host = array_shift($thost);
            if ($fservice = $v_host->configFile('services')) {
                $v_services = (igk_io_file_exists($fservice, true) ?
                    ViewHelper::Inc($fservice, ['ctrl' => $v_host]) : null) ?? [];
                if (is_null($services)){
                    $services = $v_services;
                } else 
                {
                    $services = array_merge($services, $v_services);
                }

            }
        }
        self::_UpdateService($services);
    }
}
