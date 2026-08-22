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
use IGK\System\Regex\MatchPattern;
use IGK\System\Services\InjectorProvider;
use IGK\Actions\ActionBase;
use IGK\Controllers\ControllerParams;
use IGK\Controllers\SysDbController;
use IGK\Helper\ViewHelper;
use IGK\Models\Caches\CacheModels;
use IGK\Models\Injectors\ModelBaseInjector;
use IGK\Models\ModelBase;
use IGK\Models\Users;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\OperationNotAllowedException;
use IGK\System\IInjectedArgHost;
use IGK\System\Security\CurrentUser;
use IGKException;
use IGKServices;
use IGKType;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionType;
use TypeError;
use function igk_resources_gets as __;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * default action dispactcher
 */
class Dispatcher implements IActionProcessor, IActionDispatcher
{
    /**
     * Constant: dispatch method.
     * @var mixed
     */
    const DISPATCH_METHOD = 'Dispatch';
    /**
     * Constant: instance.
     * @var mixed
     */
    const INSTANCE = IGKServices::KEY_INSTANCE;
    /**
     * auto generate doc.
     * @var null|ActionBase|IActionProcessor|object
     */
    private $m_host;
    /**
     * Property: macro.
     * @var mixed
     */
    private static $sm_macro;
    /**
     * Property: matches.
     * @var mixed
     */
    private static $sm_matches = [
        "int" => MatchPattern::Int,
        "float" => MatchPattern::Float,
    ];
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
     * auto generate doc.
     * @param string $actionName
     * @return void
     */
    public function setBaseActionName(string $actionName)
    {
        $this->m_host->baseActionName = $actionName;
    }
    /**
     * Returns Base Action Name.
     * @return string
     */
    public function getBaseActionName(): string
    {
        return $this->m_host->baseActionName;
    }
    /**
     * Returns Controller.
     */
    public function getController()
    {
        return $this->m_host ? $this->m_host->getController() : null;
    }
    /**
     * Returns Host.
     */
    public function getHost()
    {
        return $this->m_host;
    }
    /**
     * Skip verb check.
     * @param string $action_name
     */
    public function skipVerbCheck(string $action_name)
    {
        $h = $this->getHost();
        if (method_exists($h, $fc = ucfirst(__FUNCTION__))) {
            return call_user_func_array([get_class($h), $fc], [$h, $action_name]);
        }
        return false;
    }
    /**
    * auto generate doc.
    * @param callable $fc
    * @param mixed ...$args
    * @return mixed
    */
    protected static function _HandleDispatch(callable $fc, ...$args)
    {
        $g = new ReflectionFunction($fc);
        if (!(($host = $g->getClosureThis()) instanceof IInjectedArgHost)){
            $host = self::$sm_dispatcher_host;
        }
        if (!($host instanceof IInjectedArgHost)){
            $host = null;
        }
        $args = self::GetInjectArgs($g, $args, $host);
        try {
            return $fc(...$args);
        } catch (Exception $ex) {
            throw $ex;
        } catch (TypeError $ex) {
            // + | call to function but arguments injection no valid 
            throw new OperationNotAllowedException('Dispatcher failed: ' . $ex->getMessage(), 405, $ex);
        }
    }
    /**
     * Triggered when calling an inaccessible or undefined static method.
     * @param mixed $name
     * @param mixed $args
     */
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
    /**
    * Invoke.
    * @param string $name
    * @param mixed ...$args
    */
    public function invoke(string $name, ...$args)
    {
        return $this->__call($name, $args);
    }
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    private static $sm_dispatcher_host;
    /**
     * Triggered when calling an inaccessible or undefined method on an object.
     * @param mixed $name
     * @param mixed $arguments
     */
    public function __call($name, $arguments)
    {
        $v_host = $this->m_host;
        if (
            method_exists($v_host, $name)
            && (!(new ReflectionMethod($v_host, $name))->isStatic())
            && ($fc = Closure::fromCallable([$v_host, $name])->bindTo($v_host))
        ) {
            $v_host->getController()->{ControllerParams::REPLACE_URI} =
                $v_host->getDefaultEntryMethod() != $name;
            $targs = array_merge([$fc], $arguments);
            self::$sm_dispatcher_host = $this->getController();
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
    * auto generate doc.
    * @param ReflectionFunctionAbstract $g
    * @param mixed & $args
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
    * @param ?IInjectedArgHost $host
    * @throws IGKException
    * @throws ArgumentTypeNotValidException
    * @throws ReflectionException
    * @return array
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
    * auto generate doc.
    * @param array & $targs
    * @param mixed $parameters
    * @param mixed $args
    * @param ?IInjectedArgHost $host injected argument host
    * @return array
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
                    igk_die(sprintf(__('argument %s not matching %s vs %s'), $i, get_class($c), $type));
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
                        $targs[] = $v_tc;
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
                        $v_t1 = null;
                        if(($p instanceof ReflectionType) && method_exists($p , $fc = 'getName')){
                            $v_t1= call_user_func_array([$p,$fc],[]);
                        }
                        else
                            $v_t1 = (string)$p;
                        $c = self::autoCached($j, $arg, $v_t1);
                        if ($c ){
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
        self::$sm_caches = [];
        return $targs;
    }
    /**
     * 
     * @var mixed
     */
    private static $sm_caches;
    /**
     * 
     * @param mixed $j 
     * @param mixed $value 
     * @param mixed $model 
     * @return mixed 
     */
    public static function autoCached(ModelBaseInjector $j, $value, $model){
        $caches = & static::$sm_caches;
        if (is_null($caches)){
            $caches = [];
        }
        $td = get_class($j);
        if (!isset(self::$sm_caches[$td])){
            self::$sm_caches[$td] = [];
        }
        if (isset($caches[$td][$model][$value])){
            return $caches[$td][$model][$value];
        }
        $c =  $j->resolve($value, $model);
        if ($c){
            $caches[$td][$model][$value] = $c;          
            $cmodel = $model::model();
            $column = null;
            if($j instanceof ModelBaseInjector){
                $column = $j->column();
            }
            CacheModels::StoreCache( $cmodel, $value, $c , $column);
        }
        return $c;
        

    }
    /**
     * auto generate doc.
     * @param mixed &$services
     * @return void
     */
    protected static function _UpdateService(array &$services)
    {
        $lbService = IGKServices::getInstance()->services();
        foreach ($lbService as $k => $m) {
            if (isset($services[$k])) {
                $g = $services[$k];
                if(is_string($g)){
                    // just define a default class 
                    $g = [IGKServices::KEY_DEF=>$g];
                }
                $m = array_merge($m, $g); 
            }  
            $services[$k] = $m;
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
    * auto generate doc.
    * @param mixed $e
    * @return void
    */
    private static function _UseTypeCallback($e){
        list ($type) = igk_extract($e->args, 'type');
        $injects = & $e->args['injects'];
        if (!isset($injects[$type])){
            if ($type == CurrentUser::class){
                if ($c = Users::currentUser()){
                    $injects[$type] = new CurrentUser($c);
                } else{
                    $injects[$type] = null;
                }
            }
        }
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
            igk_reg_hook('sys:filter_dipatcher', [self::class, '_UseTypeCallback']);
        }
        if (is_subclass_of($type, ModelBase::class)) {
            return null;
        }
        $obj = ['type'=>$type, 'injects'=>& $injects];
        igk_hook('sys:filter_dipatcher', $obj);
        if (!($m = igk_getv($injects, $type))) {
            $refclass = igk_sys_reflect_class($type);
            $m = IGKServices::CreateServiceNewInstance($refclass, $args);
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
                if (!is_array($v_services)) {
                    $v_services = [];
                }
                if (is_null($services)) {
                    $services = $v_services;
                } else {
                    $services = array_merge($services, $v_services);
                }
            }
        }
        self::_UpdateService($services);
    }
}